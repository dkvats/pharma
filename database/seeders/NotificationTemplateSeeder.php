<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'template_key' => 'order_confirmation',
                'name' => 'Order Confirmation',
                'subject' => 'Order Confirmation - {order_number}',
                'body' => 'Dear {user_name},

Thank you for your order! Your order #{order_number} has been received and is being processed.

Order Details:
- Order Number: {order_number}
- Total Amount: ${order_total}
- Status: {order_status}
- Date: {order_date}

You will receive another notification when your order status changes.

Best regards,
The Pharma Team',
                'type' => 'email',
                'status' => 'active',
                'variables' => ['user_name', 'order_number', 'order_total', 'order_status', 'order_date'],
            ],
            [
                'template_key' => 'order_status_update',
                'name' => 'Order Status Update',
                'subject' => 'Order Update - {order_number} - {order_status}',
                'body' => 'Dear {user_name},

Your order #{order_number} status has been updated.

Status: {order_status}
Message: {status_message}

You can check your order details in your account dashboard.

Best regards,
The Pharma Team',
                'type' => 'email',
                'status' => 'active',
                'variables' => ['user_name', 'order_number', 'order_status', 'status_message'],
            ],
            [
                'template_key' => 'spin_reward',
                'name' => 'Spin Reward Notification',
                'subject' => 'Congratulations! You won {reward_name}!',
                'body' => 'Dear {user_name},

Congratulations! You have won a reward from the Spin & Win game!

Reward Details:
- Name: {reward_name}
- Value: ${reward_value}
- Type: {reward_type}

Your reward has been added to your account. Check your rewards history for more details.

Best regards,
The Pharma Team',
                'type' => 'email',
                'status' => 'active',
                'variables' => ['user_name', 'reward_name', 'reward_value', 'reward_type'],
            ],
            [
                'template_key' => 'doctor_approval_approved',
                'name' => 'Doctor Account Approved',
                'subject' => 'Your Doctor Account Has Been Approved',
                'body' => 'Dear {doctor_name},

Congratulations! Your doctor account has been approved by our admin team.

You can now log in to your account and start using all the features available to doctors, including:
- View and manage your referrals
- Track your performance and targets
- Participate in the Spin & Win game
- Access exclusive offers

Login URL: {login_url}

Welcome aboard!

Best regards,
The Pharma Team',
                'type' => 'email',
                'status' => 'active',
                'variables' => ['doctor_name', 'login_url'],
            ],
            [
                'template_key' => 'doctor_approval_rejected',
                'name' => 'Doctor Account Rejected',
                'subject' => 'Doctor Account Application Update',
                'body' => 'Dear {doctor_name},

We have reviewed your doctor account application and unfortunately, we are unable to approve it at this time.

Status: {status}

Reason: {rejection_reason}

If you believe this was a mistake or would like to reapply, please contact our support team.

Best regards,
The Pharma Team',
                'type' => 'email',
                'status' => 'active',
                'variables' => ['doctor_name', 'status', 'rejection_reason'],
            ],
            [
                'template_key' => 'store_approval_approved',
                'name' => 'Store Account Approved',
                'subject' => 'Your Store Account Has Been Approved',
                'body' => 'Dear {store_name},

Congratulations! Your store account has been approved by our admin team.

You can now log in to your account and start using all the features available to stores, including:
- Manage your inventory and stock
- Process orders and track sales
- Access exclusive store offers
- View performance reports

Login URL: {login_url}

Welcome aboard!

Best regards,
The Pharma Team',
                'type' => 'email',
                'status' => 'active',
                'variables' => ['store_name', 'store_email', 'login_url'],
            ],
            [
                'template_key' => 'store_approval_rejected',
                'name' => 'Store Account Rejected',
                'subject' => 'Store Account Application Update',
                'body' => 'Dear {store_name},

We have reviewed your store account application and unfortunately, we are unable to approve it at this time.

Status: {status}

Reason: {rejection_reason}

If you believe this was a mistake or would like to reapply, please contact our support team.

Best regards,
The Pharma Team',
                'type' => 'email',
                'status' => 'active',
                'variables' => ['store_name', 'store_email', 'status', 'rejection_reason'],
            ],
        ];

        foreach ($templates as $template) {
            $variables = json_encode($template['variables']);
            unset($template['variables']);
            
            DB::table('notification_templates')->updateOrInsert(
                ['template_key' => $template['template_key']],
                array_merge($template, [
                    'variables' => $variables,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('Notification templates seeded successfully.');
    }
}
