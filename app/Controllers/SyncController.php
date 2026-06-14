<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class SyncController extends BaseController
{
    private function parseDateTime($val)
    {
        if (empty($val)) {
            return null;
        }
        if (is_numeric($val)) {
            if ($val > 100000000000) {
                $val = (int)($val / 1000); // Milliseconds to seconds
            }
        }
        $ts = strtotime($val);
        return $ts !== false ? date('Y-m-d H:i:s', $ts) : null;
    }

    private function authenticateRequest(int &$status, string &$message, ?int &$userId = null): ?int
    {
        $request = service('request');
        $authHeader = $request->getHeaderLine('Authorization');
        
        if (empty($authHeader) || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $status = 401;
            $message = 'Missing or invalid Authorization header';
            return null;
        }

        $token = $matches[1];

        $db = \Config\Database::connect();
        $tokenRecord = $db->table('user_tokens')
                          ->where('token', $token)
                          ->where('expires_at >', date('Y-m-d H:i:s'))
                          ->get()
                          ->getRow();

        if (!$tokenRecord) {
            $status = 401;
            $message = 'Unauthorized or expired session token';
            return null;
        }

        $userId = (int)$tokenRecord->user_id;

        $user = $db->table('users')->where('id', $userId)->get()->getRow();
        if (!$user) {
            $status = 401;
            $message = 'User account not found';
            return null;
        }

        $customer = $db->table('customers')->where('id', $user->customer_id)->get()->getRow();
        if (!$customer) {
            $status = 403;
            $message = 'Customer account not found';
            return null;
        }

        if ($customer->status !== 'approved') {
            $status = 403;
            $message = 'Account pending registration approval.';
            return null;
        }

        $status = 200;
        return (int)$customer->id;
    }

    public function syncData()
    {
        $status = 200;
        $message = '';
        $userId = null;
        $customerId = $this->authenticateRequest($status, $message, $userId);

        if (!$customerId) {
            return $this->response->setJSON([
                'status'  => $status === 403 ? 'forbidden' : 'error',
                'message' => $message
            ])->setStatusCode($status);
        }

        $db = \Config\Database::connect();
        $request = service('request');
        $json = $request->getJSON(true);

        if (!$json) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Invalid JSON payload'
            ])->setStatusCode(400);
        }

        $lastSyncTime = $json['last_sync_time'] ?? null;
        $parsedLastSync = $this->parseDateTime($lastSyncTime);

        $syncedIds = [
            'contacts'                => [],
            'parties'                 => [],
            'party_contacts'          => [],
            'transactions'            => [],
            'observability_logs'      => [],
            'transaction_attachments' => []
        ];

        $db->transStart();

        // 1. Sync Contacts
        if (isset($json['contacts']) && is_array($json['contacts'])) {
            $builder = $db->table('contact_snapshots');
            foreach ($json['contacts'] as $item) {
                if (!isset($item['id'])) continue;
                $clientLocalId = (int)$item['id'];

                $data = [
                    'customer_id'       => $customerId,
                    'user_id'           => $userId,
                    'client_local_id'   => $clientLocalId,
                    'device_contact_id' => $item['deviceContactId'] ?? '',
                    'display_name'      => $item['displayName'] ?? '',
                    'primary_phone'     => $item['primaryPhone'] ?? null,
                    'phones_json'       => $item['phonesJson'] ?? '[]',
                    'last_seen_at'      => $this->parseDateTime($item['lastSeenAt']) ?? date('Y-m-d H:i:s'),
                    'phone_deleted_at'  => $this->parseDateTime($item['phoneDeletedAt'] ?? null),
                    'restored_at'       => $this->parseDateTime($item['restoredAt'] ?? null),
                    'created_at'        => $this->parseDateTime($item['createdAt'] ?? null) ?? date('Y-m-d H:i:s'),
                    'updated_at'        => date('Y-m-d H:i:s')
                ];

                $existing = $builder->where('customer_id', $customerId)
                                    ->where('user_id', $userId)
                                    ->where('client_local_id', $clientLocalId)
                                    ->get()
                                    ->getRow();

                if ($existing) {
                    $clientUpdatedAt = $this->parseDateTime($item['updatedAt'] ?? $item['lastSeenAt'] ?? $item['createdAt'] ?? null);
                    if ($clientUpdatedAt && $existing->updated_at && strtotime($existing->updated_at) > strtotime($clientUpdatedAt)) {
                        $syncedIds['contacts'][] = $clientLocalId;
                        continue;
                    }
                    unset($data['created_at']);
                }

                $builder->upsert($data);
                $syncedIds['contacts'][] = $clientLocalId;
            }
        }

        // 2. Sync Parties
        if (isset($json['parties']) && is_array($json['parties'])) {
            $builder = $db->table('parties');
            foreach ($json['parties'] as $item) {
                if (!isset($item['id'])) continue;
                $clientLocalId = (int)$item['id'];

                $data = [
                    'customer_id'                => $customerId,
                    'user_id'                    => $userId,
                    'client_local_id'            => $clientLocalId,
                    'client_contact_snapshot_id' => isset($item['contactSnapshotId']) ? (int)$item['contactSnapshotId'] : null,
                    'name'                       => $item['name'] ?? '',
                    'primary_phone'              => $item['primaryPhone'] ?? '',
                    'type'                       => $item['type'] ?? '',
                    'notes'                      => $item['notes'] ?? null,
                    'aadhaar_number'             => $item['aadhaarNumber'] ?? null,
                    'gst_number'                 => $item['gstNumber'] ?? null,
                    'aadhaar_card_path'          => $item['aadhaarCardPath'] ?? null,
                    'address_proof_path'         => $item['addressProofPath'] ?? null,
                    'is_favorite'                => (isset($item['isFavorite']) && $item['isFavorite']) ? 1 : 0,
                    'created_at'                 => $this->parseDateTime($item['createdAt'] ?? null) ?? date('Y-m-d H:i:s'),
                    'updated_at'                 => date('Y-m-d H:i:s'),
                    'deleted_at'                 => $this->parseDateTime($item['deletedAt'] ?? null)
                ];

                $existing = $builder->where('customer_id', $customerId)
                                    ->where('user_id', $userId)
                                    ->where('client_local_id', $clientLocalId)
                                    ->get()
                                    ->getRow();

                if ($existing) {
                    $clientUpdatedAt = $this->parseDateTime($item['updatedAt'] ?? $item['createdAt'] ?? null);
                    if ($clientUpdatedAt && $existing->updated_at && strtotime($existing->updated_at) > strtotime($clientUpdatedAt)) {
                        $syncedIds['parties'][] = $clientLocalId;
                        continue;
                    }
                    unset($data['created_at']);
                }

                $builder->upsert($data);
                $syncedIds['parties'][] = $clientLocalId;
            }
        }

        // 3. Sync Party Contacts
        if (isset($json['party_contacts']) && is_array($json['party_contacts'])) {
            $builder = $db->table('party_contacts');
            foreach ($json['party_contacts'] as $item) {
                if (!isset($item['id'])) continue;
                $clientLocalId = (int)$item['id'];

                $data = [
                    'customer_id'                => $customerId,
                    'user_id'                    => $userId,
                    'client_local_id'            => $clientLocalId,
                    'client_party_id'            => (int)($item['partyId'] ?? 0),
                    'client_contact_snapshot_id' => (int)($item['contactSnapshotId'] ?? 0),
                    'designation'                => $item['designation'] ?? null,
                    'notes'                      => $item['notes'] ?? null,
                    'created_at'                 => $this->parseDateTime($item['createdAt'] ?? null) ?? date('Y-m-d H:i:s'),
                    'updated_at'                 => date('Y-m-d H:i:s'),
                    'deleted_at'                 => $this->parseDateTime($item['deletedAt'] ?? null)
                ];

                $existing = $builder->where('customer_id', $customerId)
                                    ->where('user_id', $userId)
                                    ->where('client_local_id', $clientLocalId)
                                    ->get()
                                    ->getRow();

                if ($existing) {
                    $clientUpdatedAt = $this->parseDateTime($item['updatedAt'] ?? $item['createdAt'] ?? null);
                    if ($clientUpdatedAt && $existing->updated_at && strtotime($existing->updated_at) > strtotime($clientUpdatedAt)) {
                        $syncedIds['party_contacts'][] = $clientLocalId;
                        continue;
                    }
                    unset($data['created_at']);
                }

                $builder->upsert($data);
                $syncedIds['party_contacts'][] = $clientLocalId;
            }
        }

        // 4. Sync Transactions
        if (isset($json['transactions']) && is_array($json['transactions'])) {
            $builder = $db->table('transactions');
            foreach ($json['transactions'] as $item) {
                if (!isset($item['id'])) continue;
                $clientLocalId = (int)$item['id'];

                $data = [
                    'customer_id'       => $customerId,
                    'user_id'           => $userId,
                    'client_local_id'   => $clientLocalId,
                    'client_party_id'   => (int)($item['partyId'] ?? 0),
                    'type'              => $item['type'] ?? '',
                    'transaction_date'  => $this->parseDateTime($item['transactionDate'] ?? null) ?? date('Y-m-d H:i:s'),
                    'product'           => $item['product'] ?? null,
                    'quantity'          => isset($item['quantity']) ? (double)$item['quantity'] : null,
                    'unit'              => $item['unit'] ?? null,
                    'rate_paise'        => isset($item['ratePaise']) ? (int)$item['ratePaise'] : null,
                    'amount_paise'      => (int)($item['amountPaise'] ?? 0),
                    'payment_mode'      => $item['paymentMode'] ?? null,
                    'adjustment_reason' => $item['adjustmentReason'] ?? null,
                    'notes'             => $item['notes'] ?? null,
                    'location'          => $item['location'] ?? null,
                    'party_member'      => $item['partyMember'] ?? null,
                    'created_at'        => $this->parseDateTime($item['createdAt'] ?? null) ?? date('Y-m-d H:i:s'),
                    'updated_at'        => date('Y-m-d H:i:s'),
                    'deleted_at'        => $this->parseDateTime($item['deletedAt'] ?? null)
                ];

                $existing = $builder->where('customer_id', $customerId)
                                    ->where('user_id', $userId)
                                    ->where('client_local_id', $clientLocalId)
                                    ->get()
                                    ->getRow();

                if ($existing) {
                    $clientUpdatedAt = $this->parseDateTime($item['updatedAt'] ?? $item['transactionDate'] ?? $item['createdAt'] ?? null);
                    if ($clientUpdatedAt && $existing->updated_at && strtotime($existing->updated_at) > strtotime($clientUpdatedAt)) {
                        $syncedIds['transactions'][] = $clientLocalId;
                        continue;
                    }
                    unset($data['created_at']);
                }

                $builder->upsert($data);
                $syncedIds['transactions'][] = $clientLocalId;
            }
        }

        // 5. Sync Transaction Attachments
        if (isset($json['transaction_attachments']) && is_array($json['transaction_attachments'])) {
            $builder = $db->table('transaction_attachments');
            foreach ($json['transaction_attachments'] as $item) {
                if (!isset($item['id'])) continue;
                $clientLocalId = (int)$item['id'];

                $data = [
                    'customer_id'           => $customerId,
                    'user_id'               => $userId,
                    'client_local_id'       => $clientLocalId,
                    'client_transaction_id' => (int)($item['transactionId'] ?? 0),
                    'type'                  => $item['type'] ?? 'image',
                    'local_path'            => $item['localPath'] ?? '',
                    'file_name'             => $item['fileName'] ?? '',
                    'created_at'            => $this->parseDateTime($item['createdAt'] ?? null) ?? date('Y-m-d H:i:s'),
                    'deleted_at'            => $this->parseDateTime($item['deletedAt'] ?? null)
                ];

                $existing = $builder->where('customer_id', $customerId)
                                    ->where('user_id', $userId)
                                    ->where('client_local_id', $clientLocalId)
                                    ->get()
                                    ->getRow();

                if ($existing) {
                    $clientUpdatedAt = $this->parseDateTime($item['updatedAt'] ?? $item['createdAt'] ?? null);
                    if ($clientUpdatedAt && $existing->created_at && strtotime($existing->created_at) > strtotime($clientUpdatedAt)) {
                        $syncedIds['transaction_attachments'][] = $clientLocalId;
                        continue;
                    }
                    unset($data['created_at']);
                }

                $builder->upsert($data);
                $syncedIds['transaction_attachments'][] = $clientLocalId;
            }
        }

        // 6. Sync Observability Events
        if (isset($json['observability_logs']) && is_array($json['observability_logs'])) {
            $builder = $db->table('observability_events');
            foreach ($json['observability_logs'] as $item) {
                if (!isset($item['id'])) continue;
                $clientLocalId = (int)$item['id'];

                $data = [
                    'customer_id'     => $customerId,
                    'user_id'         => $userId,
                    'client_local_id' => $clientLocalId,
                    'name'            => $item['name'] ?? '',
                    'screen'          => $item['screen'] ?? null,
                    'payload_json'    => $item['payloadJson'] ?? null,
                    'created_at'      => $this->parseDateTime($item['createdAt'] ?? null) ?? date('Y-m-d H:i:s'),
                    'deleted_at'      => $this->parseDateTime($item['deletedAt'] ?? null),
                    'synced_at'       => date('Y-m-d H:i:s')
                ];

                $builder->upsert($data);
                $syncedIds['observability_logs'][] = $clientLocalId;
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Database transaction failed'
            ])->setStatusCode(500);
        }

        // Pull Sync: Fetch records changed on the server since $parsedLastSync
        $serverChanges = [
            'contacts'                => [],
            'parties'                 => [],
            'party_contacts'          => [],
            'transactions'            => [],
            'transaction_attachments' => []
        ];

        // Contacts
        $contactsQuery = $db->table('contact_snapshots')
            ->where('customer_id', $customerId)
            ->where('user_id', $userId);
        if ($parsedLastSync) {
            $contactsQuery->where('updated_at >', $parsedLastSync);
        }
        $serverChanges['contacts'] = $contactsQuery->get()->getResultArray();

        // Parties
        $partiesQuery = $db->table('parties')
            ->where('customer_id', $customerId)
            ->where('user_id', $userId);
        if ($parsedLastSync) {
            $partiesQuery->where('updated_at >', $parsedLastSync);
        }
        $serverChanges['parties'] = $partiesQuery->get()->getResultArray();

        // Party Contacts
        $partyContactsQuery = $db->table('party_contacts')
            ->where('customer_id', $customerId)
            ->where('user_id', $userId);
        if ($parsedLastSync) {
            $partyContactsQuery->where('updated_at >', $parsedLastSync);
        }
        $serverChanges['party_contacts'] = $partyContactsQuery->get()->getResultArray();

        // Transactions
        $transactionsQuery = $db->table('transactions')
            ->where('customer_id', $customerId)
            ->where('user_id', $userId);
        if ($parsedLastSync) {
            $transactionsQuery->where('updated_at >', $parsedLastSync);
        }
        $serverChanges['transactions'] = $transactionsQuery->get()->getResultArray();

        // Transaction Attachments
        $attachmentsQuery = $db->table('transaction_attachments')
            ->where('customer_id', $customerId)
            ->where('user_id', $userId);
        if ($parsedLastSync) {
            $attachmentsQuery->where('created_at >', $parsedLastSync);
        }
        $serverChanges['transaction_attachments'] = $attachmentsQuery->get()->getResultArray();

        $serverChanges = $this->castServerChanges($serverChanges);

        return $this->response->setJSON([
            'status'         => 'success',
            'synced_ids'     => $syncedIds,
            'server_changes' => $serverChanges,
            'server_time'    => date('Y-m-d H:i:s')
        ]);
    }

    private function castServerChanges(array $changes): array
    {
        if (isset($changes['contacts'])) {
            foreach ($changes['contacts'] as &$row) {
                $row['id'] = (int)$row['id'];
                $row['customer_id'] = (int)$row['customer_id'];
                $row['user_id'] = (int)$row['user_id'];
                $row['client_local_id'] = (int)$row['client_local_id'];
            }
        }
        if (isset($changes['parties'])) {
            foreach ($changes['parties'] as &$row) {
                $row['id'] = (int)$row['id'];
                $row['customer_id'] = (int)$row['customer_id'];
                $row['user_id'] = (int)$row['user_id'];
                $row['client_local_id'] = (int)$row['client_local_id'];
                $row['client_contact_snapshot_id'] = isset($row['client_contact_snapshot_id']) && $row['client_contact_snapshot_id'] !== null ? (int)$row['client_contact_snapshot_id'] : null;
                $row['is_favorite'] = (int)$row['is_favorite'];
            }
        }
        if (isset($changes['party_contacts'])) {
            foreach ($changes['party_contacts'] as &$row) {
                $row['id'] = (int)$row['id'];
                $row['customer_id'] = (int)$row['customer_id'];
                $row['user_id'] = (int)$row['user_id'];
                $row['client_local_id'] = (int)$row['client_local_id'];
                $row['client_party_id'] = (int)$row['client_party_id'];
                $row['client_contact_snapshot_id'] = (int)$row['client_contact_snapshot_id'];
            }
        }
        if (isset($changes['transactions'])) {
            foreach ($changes['transactions'] as &$row) {
                $row['id'] = (int)$row['id'];
                $row['customer_id'] = (int)$row['customer_id'];
                $row['user_id'] = (int)$row['user_id'];
                $row['client_local_id'] = (int)$row['client_local_id'];
                $row['client_party_id'] = (int)$row['client_party_id'];
                $row['quantity'] = isset($row['quantity']) && $row['quantity'] !== null ? (double)$row['quantity'] : null;
                $row['rate_paise'] = isset($row['rate_paise']) && $row['rate_paise'] !== null ? (int)$row['rate_paise'] : null;
                $row['amount_paise'] = (int)$row['amount_paise'];
            }
        }
        if (isset($changes['transaction_attachments'])) {
            foreach ($changes['transaction_attachments'] as &$row) {
                $row['id'] = (int)$row['id'];
                $row['customer_id'] = (int)$row['customer_id'];
                $row['user_id'] = (int)$row['user_id'];
                $row['client_local_id'] = (int)$row['client_local_id'];
                $row['client_transaction_id'] = (int)$row['client_transaction_id'];
            }
        }
        return $changes;
    }

    public function uploadFileSync()
    {
        $status = 200;
        $message = '';
        $userId = null;
        $customerId = $this->authenticateRequest($status, $message, $userId);

        if (!$customerId) {
            return $this->response->setJSON([
                'status'  => $status === 403 ? 'forbidden' : 'error',
                'message' => $message
            ])->setStatusCode($status);
        }

        $request = service('request');

        $clientLocalId = $request->getPost('client_local_id');
        $clientTxId = $request->getPost('client_transaction_id');
        $type = $request->getPost('type') ?? 'image';

        if (!$clientLocalId) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Missing client_local_id text parameter'
            ])->setStatusCode(400);
        }

        $file = $request->getFile('file');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Missing or invalid file'
            ])->setStatusCode(400);
        }

        $uploadPath = ROOTPATH . 'public/uploads/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $newName = $file->getRandomName();
        if ($file->move($uploadPath, $newName)) {
            $serverPath = '/uploads/' . $newName;

            $db = \Config\Database::connect();
            $builder = $db->table('transaction_attachments');

            // Delete old file if it exists to avoid dangling leaks on retries
            $existing = $builder->where('customer_id', $customerId)
                                ->where('user_id', $userId)
                                ->where('client_local_id', (int)$clientLocalId)
                                ->get()
                                ->getRow();

            if ($existing && !empty($existing->server_path)) {
                $oldFilePath = ROOTPATH . 'public' . $existing->server_path;
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            // Update local attachments table on the server to record the uploaded path
            $builder->where('customer_id', $customerId)
                    ->where('user_id', $userId)
                    ->where('client_local_id', (int)$clientLocalId)
                    ->update([
                        'server_path' => $serverPath
                    ]);

            return $this->response->setJSON([
                'status'          => 'success',
                'client_local_id' => (int)$clientLocalId,
                'server_path'     => $serverPath
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Failed to store file'
        ])->setStatusCode(500);
    }
}
