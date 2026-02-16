<?php

namespace Modules\NotificationTemplate\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Constant\Models\Constant;
use Modules\NotificationTemplate\Models\NotificationTemplate;

class NotificationTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Disable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        /*
         * NotificationTemplates Seed
         * ------------------
         */

        // DB::table('notificationtemplates')->truncate();
        // echo "Truncate: notificationtemplates \n";

        $types = [
            [
                'type' => 'notification_type',
                'value' => 'new_booking',
                'name' => 'New Booking',
            ],
            [
                'type' => 'notification_type',
                'value' => 'accept_booking',
                'name' => 'Accept Booking',
            ],
            [
                'type' => 'notification_type',
                'value' => 'reject_booking',
                'name' => 'Reject Booking',
            ],
            [
                'type' => 'notification_type',
                'value' => 'complete_booking',
                'name' => 'Complete On Booking',
            ],
            [
                'type' => 'notification_type',
                'value' => 'accept_booking_request',
                'name' => 'Accept Booking Request',
            ],
            [
                'type' => 'notification_type',
                'value' => 'cancel_booking',
                'name' => 'Cancel On Booking',
            ],
            [
                'type' => 'notification_type',
                'value' => 'change_password',
                'name' => 'Chnage Password',
            ],
            [
                'type' => 'notification_type',
                'value' => 'forget_email_password',
                'name' => 'Forget Email/Password',
            ],
            [
                'type' => 'notification_type',
                'value' => 'order_placed',
                'name' => 'Order Placed',
            ],
            [
                'type' => 'notification_type',
                'value' => 'order_accepted',
                'name' => 'Order Accepted',
            ],
            [
                'type' => 'notification_type',
                'value' => 'order_proccessing',
                'name' => 'Order Processing',
            ],
            [
                'type' => 'notification_type',
                'value' => 'order_delivered',
                'name' => 'Order Delivered',
            ],
            [
                'type' => 'notification_type',
                'value' => 'order_cancelled',
                'name' => 'Oreder Cancelled',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'id',
                'name' => 'ID',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'user_name',
                'name' => 'Customer Name',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'description',
                'name' => 'Description / Note',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'booking_id',
                'name' => 'Booking ID',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'booking_date',
                'name' => 'Booking Date',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'booking_time',
                'name' => 'Booking Time',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'booking_services_names',
                'name' => 'Booking Services Names',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'booking_duration',
                'name' => 'Booking Duration',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'employee_name',
                'name' => 'Staff Name',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'venue_address',
                'name' => 'Venue / Address',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'logged_in_user_fullname',
                'name' => 'Your Name',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'logged_in_user_role',
                'name' => 'Your Position',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'company_name',
                'name' => 'Company Name',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'company_contact_info',
                'name' => 'Company Info',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'user_id',
                'name' => 'User\' ID',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'user_password',
                'name' => 'User Password',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'link',
                'name' => 'Link',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'site_url',
                'name' => 'Site URL',
            ],
            [
                'type' => 'notification_to',
                'value' => 'user',
                'name' => 'User',
            ],

            [
                'type' => 'notification_to',
                'value' => 'employee',
                'name' => 'Employee',
            ],

            [
                'type' => 'notification_to',
                'value' => 'demo_admin',
                'name' => 'Demo Admin',
            ],
            [
                'type' => 'notification_to',
                'value' => 'admin',
                'name' => 'Admin',
            ],
        ];

        foreach ($types as $value) {
            Constant::updateOrCreate(['type' => $value['type'], 'value' => $value['value']], $value);
        }

        //echo " Insert: notificationtempletes \n\n";

        // Enable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('notification_templates')->delete();
        DB::table('notification_template_content_mapping')->delete();

        $template = NotificationTemplate::create([
            'type' => 'new_booking',
            'name' => 'new_booking',
            'label' => 'Booking confirmation',
            'status' => 1,
            'to' => '["admin","demo_admin","employee","user"]',
            'channels' => ['IS_MAIL' => '0','PUSH_NOTIFICATION' => '1','IS_CUSTOM_WEBHOOK' => '0'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'user_type' => 'admin',
            'status' => 1,
            'mail_subject' => 'New Booking Received',
            'mail_template_detail' => '<p>New booking received: ID #[[ booking_id ]] by [[ user_name ]]. Check the details on your dashboard.</p>',
            'subject' => 'New Booking Received.',
            'template_detail' => "<p>Dear [[ admin_name ]],<br /><br /> We are pleased to inform you that your appointment has been successfully confirmed with [[ company_name ]]! Thank you for choosing us &ndash; we are excited to serve you and ensure you and your pet have a fantastic experience. <br /><br />Here are the details of your appointment: <br /><br />📅 Appointment Details: <br />- Appointment ID: #[[ booking_id ]] - Service/Event: [[ booking_services_names ]]<br />- Date: [[ booking_date ]] <br />- Time: [[ booking_time ]] <br />- Location: [[ venue_address ]] <br /><br />We&rsquo;ve received your appointment and everything is set. Our team is preparing to deliver an amazing service, and we want to make sure all your expectations are met. If you have any questions or specific requests, feel free to reach out to us. <br /><br />Tip: Don't forget to mark your calendar and set a reminder for your appointment! <br /><br />We appreciate your trust in us and look forward to providing top-notch service. For any further queries, our customer support team is here to assist you.<br /><br />Best regards,<br />[[ logged_in_user_fullname ]], <br />[[ logged_in_user_role ]],<br />[[ company_name ]]</p>",
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'status' => 1,
            'user_type' => 'demo_admin',
            'mail_subject' => 'New Booking Received',
            'mail_template_detail' => '<p>New booking received: ID #[[ booking_id ]] by [[ user_name ]]. Check the details on your dashboard.</p>',
            'subject' => 'New Booking Received.',
            'template_detail' => "<p>Dear [[ admin_name ]],<br /><br /> We are pleased to inform you that your appointment has been successfully confirmed with [[ company_name ]]! Thank you for choosing us &ndash; we are excited to serve you and ensure you and your pet have a fantastic experience. <br /><br />Here are the details of your appointment: <br /><br />📅 Appointment Details: <br />- Appointment ID: #[[ booking_id ]] - Service/Event: [[ booking_services_names ]]<br />- Date: [[ booking_date ]] <br />- Time: [[ booking_time ]] <br />- Location: [[ venue_address ]] <br /><br />We&rsquo;ve received your appointment and everything is set. Our team is preparing to deliver an amazing service, and we want to make sure all your expectations are met. If you have any questions or specific requests, feel free to reach out to us. <br /><br />Tip: Don't forget to mark your calendar and set a reminder for your appointment! <br /><br />We appreciate your trust in us and look forward to providing top-notch service. For any further queries, our customer support team is here to assist you.<br /><br />Best regards,<br />[[ logged_in_user_fullname ]], <br />[[ logged_in_user_role ]],<br />[[ company_name ]]</p>",
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'status' => 1,
            'user_type' => 'employee',
            'mail_subject' => 'New Booking Assigned',
            'subject' => 'New Booking Assigned.',
            'mail_template_detail' => 'New booking assigned to you: ID #[[ booking_id ]]. Check schedule for [[ booking_date ]] at [[ booking_time ]].',
            'template_detail' => '<p>Hello [[ employee_name ]],<br /><br /> You have been assigned a new booking scheduled for [[ booking_date ]] at [[ booking_time ]]. <br /><br />📋 Booking Details: <br />- Booking ID: #[[ booking_id ]] <br />- Customer Name: [[ user_name ]] <br />- Services: [[ booking_services_names ]] <br />- Duration: [[ booking_duration ]] <br />- Address: [[ venue_address ]] <br /><br /> Please prepare accordingly and confirm the booking in your dashboard. <br /><br /> Regards, <br /> [[ company_name ]]</p>',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'status' => 1,
            'user_type' => 'user',
            'mail_subject' => 'Your Booking Confirmation',
            'mail_template_detail' =>'<p>Your booking (ID #[[ booking_id ]]) is confirmed for [[ booking_date ]] at [[ booking_time ]]. Details sent to your email.</p>',
            'subject' => 'Your Booking Confirmation',
            'template_detail' => '<p>Hello [[ user_name ]],<br /><br /> Thank you for choosing [[ company_name ]] for your pet care needs! Your booking has been confirmed. <br /><br /> 📋 Booking Details:<br /> - Booking ID: #[[ booking_id ]] <br />- Date &amp; Time: [[ booking_date ]] at [[ booking_time ]] <br />- Services: [[ booking_services_names ]] <br />- Duration: [[ booking_duration ]] <br />- Assigned Staff: [[ employee_name ]] <br />- Address: [[ venue_address ]] If you have any questions, please reach out to us. <br /><br /> Thank you, <br /> [[ company_name ]]</p>',
        ]);


        $template = NotificationTemplate::create([
            'type' => 'accept_booking',
            'name' => 'accept_booking',
            'label' => 'Accept Booking',
            'status' => 1,
            'to' => '["user","admin","demo_admin"]',
            'channels' => ['IS_MAIL' => '0','PUSH_NOTIFICATION' => '1','IS_CUSTOM_WEBHOOK' => '0'],
        ]);
        $template->defaultNotificationTemplateMap()->create([

            'language' => 'en',
            'user_type' => 'user',
            'status' => 1,
            'mail_subject' => 'Booking Confirmed',
            'subject' => 'Booking Confirmed',
            'mail_template_detail' => '<p dir="ltr">Your booking is confirmed! Booking ID: #[[ booking_id ]]. Check your email for full details.</p>',
            'template_detail' =>"<p>Dear [[ user_name ]],<br /><br /> Your booking has been successfully accepted! We're excited to welcome you and hope you enjoy your time with us. <br /><br /> Booking Details: <br />- Booking ID: #[[ booking_id ]]<br />- Booking Date: [[ booking_date ]] <br />- Booking Time: [[ booking_time ]] <br /><br /> We look forward to hosting you and ensuring you have a pleasant experience. If you have any questions or need assistance, don't hesitate to reach out to us. <br /><br /> Best regards, <br /> The [[ company_name ]] Team <br /> Contact: [[ company_contact_info ]]</p>
            ",
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'user_type' => 'admin',
            'status' => 1,
            'mail_subject' => 'New Booking Accepted',
            'mail_template_detail' => '
           <p dir="ltr">New booking accepted: ID #[[ booking_id ]] from [[ user_name ]]. Review the details in your admin panel.</p>',
            'subject' => 'New Booking Accepted',
            'template_detail' => '<p>Dear [[ admin_name ]], <br /><br /> A new booking has been accepted! Please review the details below. <br /><br /> Booking Details: <br /><br />- Booking ID: #[[ booking_id ]] <br />- User Name: [[ user_name ]] <br />- Booking Date: [[ booking_date ]] <br />- Booking Time: [[ booking_time ]] <br /><br /> If you need further details or assistance, please contact us. <br /><br /> Best regards, <br /> The [[ company_name ]] Team <br /> Contact: [[ company_contact_info ]]</p>
<p><span style="font-family: Arial; font-size: 13px; white-space-collapse: preserve;">&nbsp;</span></p>
            ',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'A booking has been accepted.',
            'user_type' => 'demo_admin',
            'status' => 1,
            'mail_subject' => 'New Booking Accepted',
            'mail_template_detail' => '
           <p dir="ltr">New booking accepted: ID #[[ booking_id ]] from [[ user_name ]]. Review the details in your admin panel.</p>',
            'subject' => 'New Booking Accepted',
            'template_detail' => '
               <p>Dear [[ admin_name ]], <br /><br /> A new booking has been accepted! Please review the details below. <br /><br /> Booking Details: <br /><br />- Booking ID: #[[ booking_id ]] <br />- User Name: [[ user_name ]] <br />- Booking Date: [[ booking_date ]] <br />- Booking Time: [[ booking_time ]] <br /><br /> If you need further details or assistance, please contact us. <br /><br /> Best regards, <br /> The [[ company_name ]] Team <br /> Contact: [[ company_contact_info ]]</p>
<p><span style="font-family: Arial; font-size: 13px; white-space-collapse: preserve;">&nbsp;</span></p>
            ',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'reject_booking',
            'name' => 'reject_booking',
            'label' => 'Reject Booking',
            'status' => 1,
            'to' => '["user","admin","demo_admin"]',
            'channels' => ['IS_MAIL' => '0','PUSH_NOTIFICATION' => '1','IS_CUSTOM_WEBHOOK' => '0'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'user_type' => 'user',
            'status' => 1,
            'mail_subject' => 'Booking Rejected',
            'mail_template_detail' => '<p dir="ltr">We regret to inform you that your booking #[[ booking_id ]] has been rejected. Please check your email for details.</p>',
            'subject' => 'Booking Rejected',
            'template_detail' => '
                <p>Dear [[ user_name ]], <br /><br /> We regret to inform you that your booking has been rejected. We sincerely apologize for any inconvenience caused. <br /><br /> Booking Details:<br />- Booking ID: #[[ booking_id ]]<br /> - Service: [[ booking_services_names ]] <br />- Date: [[ booking_date ]] <br />- Time: [[ booking_time ]] <br />- Location: [[ venue_address ]] <br /><br /> If you have any questions or need assistance, please do not hesitate to contact us. <br /><br /> Best regards, <br /> The [[ company_name ]] <br />Team Contact: [[ company_contact_info ]]</p>

            ',
        ]);


        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'user_type' => 'admin',
            'status' => 1,
            'mail_subject' => 'Booking Rejected',
            'mail_template_detail' => '<p>Booking #[[ booking_id ]] has been rejected. Please review the details in your admin panel.</p>',
            'subject' => 'Booking Rejected',
            'template_detail' => '
               <p>Dear [[ admin_name ]],<br /><br /> A booking has been rejected. Please review the details and take any necessary actions.</p>

            ',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',

            'user_type' => 'demo_admin',
            'status' => 1,
            'mail_subject' => 'Booking Rejected',
            'mail_template_detail' => '<p>Booking #[[ booking_id ]] has been rejected. Please review the details in your admin panel.</p>',
            'subject' => 'Booking Rejected',
            'template_detail' => '
               <p>Dear [[ admin_name ]],<br /><br /> A booking has been rejected. Please review the details and take any necessary actions.</p>

            ',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'complete_booking',
            'name' => 'complete_booking',
            'label' => 'Complete On Booking',
            'status' => 1,
            'to' => '["user","admin","demo_admin"]',
            'channels' => ['IS_MAIL' => '0','PUSH_NOTIFICATION' => '1','IS_CUSTOM_WEBHOOK' => '0'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'user_type' => 'user',
            'status' => 1,
            'mail_subject' => 'Booking Complete',
            'mail_template_detail' => '<p>Your booking #[[ booking_id ]] for [[ booking_services_names ]] on [[ booking_date ]] is complete. We hope you had a great experience!</p>',
            'subject' => 'Booking Complete',
            'template_detail' => '<p>Dear [[ user_name ]],</p>
<p>Congratulations! Your booking has been successfully completed. We sincerely appreciate your business and hope you had a wonderful experience with us.</p>
<p>Booking Date: [[ booking_date ]]</p>
<p>Service Provided: [[ booking_services_names ]]</p>
<p>We look forward to serving you again in the future. If you have any feedback or need further assistance, please do not hesitate to contact us.</p>
<p>Best regards,</p>
<p>[[ company_name ]]</p>
<p>[[ company_contact_info ]]</p>
            ',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'status' => 1,
            'user_type' => 'admin',
            'mail_subject' => 'Booking Completed Successfully',
            'mail_template_detail' => '<p>We regret to inform you that your booking #[[ booking_id ]] has been rejected. Please check your email for details.</p>',
            'subject' => 'Booking Completed Successfully',
            'template_detail' => '
            <p>Dear [[ user_name ]], <br /><br /> We regret to inform you that your booking has been rejected. We sincerely apologize for any inconvenience caused. <br /><br /> Booking Details: <br />- Booking ID: #[[ booking_id ]] <br />- Service: [[ booking_services_names ]] <br />- Date: [[ booking_date ]] <br />- Time: [[ booking_time ]] <br />- Location: [[ venue_address ]] <br /><br /> If you have any questions or need assistance, please do not hesitate to contact us. <br /><br />Best regards, <br /> The [[ company_name ]] Team <br /> Contact: [[ company_contact_info ]]</p>
          ',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'user_type' => 'demo_admin',
            'status' => 1,
            'mail_subject' => 'Booking Completed Successfully',
            'mail_template_detail' => '<p>We regret to inform you that your booking #[[ booking_id ]] has been rejected. Please check your email for details.</p>',
            'subject' => 'Booking Completed Successfully',
            'template_detail' => '
            <p>Dear [[ user_name ]], <br /><br /> We regret to inform you that your booking has been rejected. We sincerely apologize for any inconvenience caused. <br /><br /> Booking Details: <br />- Booking ID: #[[ booking_id ]] <br />- Service: [[ booking_services_names ]] <br />- Date: [[ booking_date ]] <br />- Time: [[ booking_time ]] <br />- Location: [[ venue_address ]] <br /><br /> If you have any questions or need assistance, please do not hesitate to contact us. <br /><br />Best regards, <br /> The [[ company_name ]] Team <br /> Contact: [[ company_contact_info ]]</p>
          ',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'cancel_booking',
            'name' => 'cancel_booking',
            'label' => 'Cancel On Booking',
            'status' => 1,
            'to' => '["user","employee","admin","demo_admin"]',
            'channels' => ['IS_MAIL' => '0','PUSH_NOTIFICATION' => '1','IS_CUSTOM_WEBHOOK' => '0'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'status' => 1,
            'user_type' => 'user',
            'mail_subject' => 'Your Booking Has Been Cancelled',
            'mail_template_detail' => "<p>Your booking #[[ booking_id ]] for [[ booking_services_names ]] on [[ booking_date ]] has been cancelled. Please contact us for further assistance.</p>",
            'subject' => 'Your Booking Has Been Cancelled',
            'template_detail' => '<p>Dear [[ user_name ]], <br /><br /> We regret to inform you that your booking has been cancelled. We apologize for any inconvenience this may have caused.</p>
            <p>Booking Details: <br />- Booking ID: #[[ booking_id ]] <br />- Service: [[ booking_services_names ]] <br />- Date: [[ booking_date ]] <br />- Time: [[ booking_time ]] <br />- Location: [[ venue_address ]] <br /><br /> If you have any questions or need further assistance, please do not hesitate to contact us. We are here to help! <br /><br /> Best regards, <br /> The [[ company_name ]] Team <br /> Contact: [[ company_contact_info ]]</p>'
            ,
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'status' => 1,
            'user_type' => 'employee',
            'mail_subject' => 'Booking Cancelled',
            'mail_template_detail' => '<p>The booking #[[ booking_id ]] for [[ booking_services_names ]] has been cancelled.&nbsp;</p>',
            'subject' => 'Booking Cancelled',
            'template_detail' => '<p>Dear [[ employee_name ]],<br /><br />A booking has been cancelled. Please review the details below.<br /><br />Booking Details:<br />- Booking ID: #[[ booking_id ]]<br />- User Name: [[ user_name ]]<br />- Service: [[ booking_services_names ]]<br />- Date: [[ booking_date ]]<br />- Time: [[ booking_time ]]<br />- Location: [[ venue_address ]]<br /><br />Please take the necessary actions as required.<br />Best regards,<br />The [[ company_name ]] Team<br />Contact: [[ company_contact_info ]]</p>

            ',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'status' => 1,
            'user_type' => 'admin',
            'mail_subject' => 'Booking Cancelled',
            'mail_template_detail' => '<p>The booking #[[ booking_id ]] for [[ booking_services_names ]] has been cancelled. Please review the details in your admin dashboard.</p>',
            'subject' => 'Booking Cancelled',
            'template_detail' => ' <p>Dear [[ admin_name ]],<br /><br /> A booking has been cancelled. Please review the details below.<br /><br /> Booking Details: <br />- Booking ID: #[[ booking_id ]] <br />- User Name: [[ user_name ]] <br />- Service: [[ booking_services_names ]] <br />- Date: [[ booking_date ]] <br />- Time: [[ booking_time ]] <br />- Location: [[ venue_address ]] <br /><br /> Please take the necessary actions as required.<br />Best regards, <br /> The [[ company_name ]] Team <br /> Contact: [[ company_contact_info ]]</p>',
        ]);


        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'The booking has been cancelled. Review the details as needed.',
            'status' => 1,
            'user_type' => 'demo_admin',
            'mail_subject' => 'Booking Cancelled',
            'mail_template_detail' => '<p>The booking #[[ booking_id ]] for [[ booking_services_names ]] has been cancelled. Please review the details in your admin dashboard.</p>',
            'subject' => 'Booking Cancelled',
            'template_detail' => ' <p>Dear [[ admin_name ]],<br /><br /> A booking has been cancelled. Please review the details below.<br /><br /> Booking Details: <br />- Booking ID: #[[ booking_id ]] <br />- User Name: [[ user_name ]] <br />- Service: [[ booking_services_names ]] <br />- Date: [[ booking_date ]] <br />- Time: [[ booking_time ]] <br />- Location: [[ venue_address ]] <br /><br /> Please take the necessary actions as required.<br />Best regards, <br /> The [[ company_name ]] Team <br /> Contact: [[ company_contact_info ]]</p>',

        ]);


        $template = NotificationTemplate::create([
            'type' => 'accept_booking_request',
            'name' => 'accept_booking_request',
            'label' => 'Accept Booking Request',
            'status' => 1,
            'to' => '["admin","demo_admin","user"]',
            'channels' => ['IS_MAIL' => '0','PUSH_NOTIFICATION' => '1','IS_CUSTOM_WEBHOOK' => '0'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'status' => 1,
            'user_type' => 'admin',
            'mail_subject' => 'Booking Accepted',
            'mail_template_detail' => '<p>A booking #[[ booking_id ]] for [[ booking_services_names ]] has been accepted. Check the admin dashboard for more details.</p>',
            'subject' => 'Booking Accepted',
            'template_detail' => ' <p>Dear [[ admin_name ]], <br /><br /> A new booking request has been accepted. Please review the details below. <br /><br /> Booking Details:<br /> - Booking ID: #[[ booking_id ]] <br />- User Name: [[ user_name ]] <br />- Service(s): [[ booking_services_names ]] <br />- Date: [[ booking_date ]] <br />- Time: [[ booking_time ]] <br />- Duration: [[ booking_duration ]] <br />- Venue: [[ venue_address ]]<br /> <br /> Best regards, <br /> The [[ company_name ]] Team <br />Contact us: [[ company_contact_info ]]</p>',
        ]);


        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'status' => 1,
            'user_type' => 'demo_admin',
            'mail_subject' => 'Booking Accepted',
            'mail_template_detail' => '<p>A booking #[[ booking_id ]] for [[ booking_services_names ]] has been accepted. Check the admin dashboard for more details.</p>',
            'subject' => 'Booking Accepted',
            'template_detail' => '<p>Dear [[ admin_name ]], <br /><br /> A new booking request has been accepted. Please review the details below. <br /><br /> Booking Details:<br /> - Booking ID: #[[ booking_id ]] <br />- User Name: [[ user_name ]] <br />- Service(s): [[ booking_services_names ]] <br />- Date: [[ booking_date ]] <br />- Time: [[ booking_time ]] <br />- Duration: [[ booking_duration ]] <br />- Venue: [[ venue_address ]]<br /> <br /> Best regards, <br /> The [[ company_name ]] Team <br />Contact us: [[ company_contact_info ]]</p>',

        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'status' => 1,
            'user_type' => 'user',
            'mail_subject' => 'Booking Accepted',
            'mail_template_detail' => '<p>Your booking #[[ booking_id ]] for [[ booking_services_names ]] on [[ booking_date ]] at [[ booking_time ]] has been accepted. See you soon!</p>',
            'subject' => 'Your Booking Request Has Been Accepted!',
            'template_detail' => '<p>Dear [[ user_name ]], <br /><br /> Great news! Your booking request has been accepted. We look forward to providing you with exceptional service. <br /><br /> Booking Details: <br />- Booking ID: #[[ booking_id ]] <br />- Service(s): [[ booking_services_names ]] <br />- Date: [[ booking_date ]] <br />- Time: [[ booking_time ]] <br />- Duration: [[ booking_duration ]] <br />- Venue: [[ venue_address ]] <br /><br /> For any questions, please contact us at [[ company_contact_info ]].<br /><br /> Best regards, <br /> The [[ company_name ]] Team</p>
<p><span style="font-family: Arial; font-size: 13px; white-space-collapse: preserve;">&nbsp;</span></p>
            ',
        ]);


        $template = NotificationTemplate::create([
            'type' => 'change_password',
            'name' => 'change_password',
            'label' => 'Change Password',
            'status' => 1,
            'to' => '["admin","demo_admin","employee","user"]',
            'channels' => ['IS_MAIL' => '0','PUSH_NOTIFICATION' => '1','IS_CUSTOM_WEBHOOK' => '0'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'status' => 1,
            'user_type' => 'admin',
            'mail_subject' => 'User Password Changed',
            'mail_template_detail' => '<p>User [[ user_name ]] ( ID: [[ user_id ]] ) has changed their password. Please review if necessary.</p>',
            'subject' => 'User Password Updated',
            'template_detail' => '<p>Dear [[ admin_name ]],<br /><br /> A user has successfully changed their password. Please review the details below: <br /><br /> User ID: [[ user_id ]] <br /> User Name: [[ user_name ]]<br /><br /> If this change was unauthorized, please investigate further. <br /><br /> Best regards, <br /> The [[ company_name ]] Team <br /> Contact us: [[ company_contact_info ]]</p>
<p><span style="font-family: Arial; font-size: 13px; white-space-collapse: preserve;">&nbsp;</span></p>
            ',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'A password change has occurred. If you did not make this change, take action to secure the account.',
            'status' => 1,
            'user_type' => 'demo_admin',
            'mail_subject' => 'User Password Changed',
            'mail_template_detail' => '<p>User [[ user_name ]] ( ID: [[ user_id ]] ) has changed their password. Please review if necessary.</p>',
            'subject' => 'User Password Updated',
            'template_detail' => '<p>Dear [[ admin_name ]],<br /><br /> A user has successfully changed their password. Please review the details below: <br /><br /> User ID: [[ user_id ]] <br /> User Name: [[ user_name ]]<br /><br /> If this change was unauthorized, please investigate further. <br /><br /> Best regards, <br /> The [[ company_name ]] Team <br /> Contact us: [[ company_contact_info ]]</p>
<p><span style="font-family: Arial; font-size: 13px; white-space-collapse: preserve;">&nbsp;</span></p>
            ',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'status' => 1,
            'user_type' => 'employee',
            'mail_subject' => 'Password Changed Successfully',
            'mail_template_detail' => "<p>Your password has been updated. If this wasn't you, contact support immediately.</p>",
            'subject' => 'Your Password Has Been Successfully Changed',
            'template_detail' => '<p>Dear [[ employee_name ]], <br /><br /> Your password has been successfully changed. <br /><br />If you did not request this change, please contact us immediately. <br /><br />For any concerns, reach out to us at [[ company_contact_info ]]. <br /><br /> Best regards, <br />The [[ company_name ]] Team</p>
            ',
        ]);


        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Your password has been changed successfully. If you did not initiate this change, please follow the steps to secure your account.',
            'status' => 1,
            'user_type' => 'user',
            'subject' => 'Your Password Has Been Successfully Changed',
            'mail_subject' => 'Password Changed Successfully',
            'mail_template_detail' => "<p>Your password has been updated. If this wasn't you, contact support immediately.</p>",
            'template_detail' => '<p>Dear [[ user_name ]], <br /><br /> Your password has been successfully changed.<br /><br /> If you did not request this change, please contact us immediately.<br /><br /> For any concerns, reach out to us at [[ company_contact_info ]]. <br /><br /> Best regards, <br /> The [[ company_name ]] Team</p>

            ',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'forget_email_password',
            'name' => 'forget_email_password',
            'label' => 'Forget Email/Password',
            'status' => 1,
            'to' => '["admin","demo_admin","employee","user"]',
            'channels' => ['IS_MAIL' => '0','PUSH_NOTIFICATION' => '1','IS_CUSTOM_WEBHOOK' => '0'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'status' => 1,
            'user_type' => 'user',
            'mail_subject' => 'User Requested Password Reset',
            'mail_template_detail' => '<p>User [[ user_name ]] ( ID: [[ user_id ]] ) requested a password reset. Review if needed.</p>',
            'subject' => 'Password Reset Request by User',
            'template_detail' => '<p>Thank you for choosing us for your recent order. We are delighted to confirm that your order has been successfully placed!</p>
            ',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'status' => 1,
            'user_type' => 'admin',
            'mail_subject' => 'Password Reset Request by User',
            'mail_template_detail' => '<p>User [[ user_name ]] ( ID: [[ user_id ]] ) requested a password reset. Review if needed.</p>',
            'subject' => 'User Requested Password Reset',
            'template_detail' => '<p>Dear [[ admin_name ]], <br /><br />A user has requested a password reset.<br /><br /> Please review the details below: <br /> User ID: [[ user_id ]]<br />User Name: [[ user_name ]] <br /><br />If this request seems suspicious, please investigate. <br /><br /> Best regards,<br /> The [[ company_name ]] Team <br /> Contact: [[ company_contact_info ]]</p>
            ',
        ]);


        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'status' => 1,
            'user_type' => 'demo_admin',
           'mail_subject' => 'Password Reset Request by User',
            'mail_template_detail' => '<p>User [[ user_name ]] ( ID: [[ user_id ]] ) requested a password reset. Review if needed.</p>',
            'subject' => 'User Requested Password Reset',
            'template_detail' => '<p>Dear [[ admin_name ]], <br /><br />A user has requested a password reset.<br /><br /> Please review the details below: <br /> User ID: [[ user_id ]]<br />User Name: [[ user_name ]] <br /><br />If this request seems suspicious, please investigate. <br /><br /> Best regards,<br /> The [[ company_name ]] Team <br /> Contact: [[ company_contact_info ]]</p>
            ',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'status' => 1,
            'user_type' => 'employee',
             'mail_subject' => 'Reset Your Account Password',
            'mail_template_detail' => '<p>We received your password reset request. Check your email for instructions<span style="font-family: Arial; font-size: 13px; white-space-collapse: preserve;">.</span></p>',
            'subject' => 'Reset Your Account Password',
            'template_detail' => '
              <p>Dear [[ employee_name ]], <br /><br />We received a request to reset your password. <br /><br />Please click the link below to set a new password:<br />Reset Link: [[ link ]]<br /><br /> If you did not request this, please ignore this email or contact us immediately. <br /><br /> Best regards, <br /> The [[ company_name ]] Team <br /> Contact us: [[ company_contact_info ]]</p>
<p><span style="font-family: Arial; font-size: 13px; white-space-collapse: preserve;">&nbsp;</span></p>
            ',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'order_placed',
            'name' => 'order_placed',
            'label' => 'Order Placed',
            'status' => 1,
            'to' => '["user","admin","demo_admin","employee"]',
            'channels' => ['IS_MAIL' => '0','PUSH_NOTIFICATION' => '1','IS_CUSTOM_WEBHOOK' => '0'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'status' => 1,
            'user_type' => 'user',
            'mail_subject' => 'Your Pet Care Product Order is Confirmed!',
            'mail_template_detail' => '<p>Your order for pet care products has been successfully placed. We will update you once it is shipped!</p>',
            'subject' => 'Your Pet Care Product Order is Confirmed!',
            'template_detail' => '
            <p>Dear [[ user_name ]], <br /><br /> Thank you for your purchase! We are happy to inform you that your order for pet care products has been successfully placed. <br /><br />If you have any questions or need assistance, feel free to contact us at [[ company_contact_info ]]. <br /><br /> Thank you for trusting [[ company_name ]] for your pet care needs!<br /><br /> Best regards, <br /> The [[ company_name ]] Team <br /> Contact us: [[ company_contact_info ]]</p>
            ',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'status' => 1,
            'user_type' => 'admin',
            'mail_subject' => 'New Pet Care Product Order',
            'subject' => 'New Pet Care Product Order',
            'mail_template_detail' => '<p>A new order for pet care products has been placed. Please review the order details and take action.</p>' ,
            'template_detail' => '
              <p>Dear [[ admin_name ]],<br /><br /> A new order for pet care products has been placed.<br /><br />Please ensure the order is processed and shipped promptly. <br /><br /> Best regards, <br /><br /> The [[ company_name ]] Team &nbsp;</p>
<p>Contact: [[ company_contact_info ]]</p>
            ',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'status' => 1,
            'user_type' => 'demo_admin',
            'mail_subject' => 'New Pet Care Product Order',
            'subject' => 'New Pet Care Product Order',
            'mail_template_detail' => '<p>A new order for pet care products has been placed. Please review the order details and take action.</p>'
            ,
            'template_detail' => '
              <p>Dear [[ admin_name ]],<br /><br /> A new order for pet care products has been placed.<br /><br />Please ensure the order is processed and shipped promptly. <br /><br /> Best regards, <br /><br /> The [[ company_name ]] Team &nbsp;</p>
<p>Contact: [[ company_contact_info ]]</p>
            ',
        ]);


        $template = NotificationTemplate::create([
            'type' => 'order_accepted',
            'name' => 'order_accepted',
            'label' => 'Order Accepted',
            'status' => 1,
            'to' => '["user","admin","demo_admin"]',
            'channels' => ['IS_MAIL' => '0','PUSH_NOTIFICATION' => '1','IS_CUSTOM_WEBHOOK' => '0'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'status' => 1,
            'user_type' => 'user',
            'mail_subject' => 'Your Pet Care Product Order is Accepted!',
            'mail_template_detail' => '<p>Your order for pet care products has been accepted and is being processed. We&rsquo;ll notify you when it&rsquo;s shipped!</p>',
            'subject' => 'Your Pet Care Product Order is Accepted!',
            'template_detail' => "<p>Dear [[ user_name ]],</p>
<p>Great news! Your order for pet care products has been successfully accepted and is being processed. <br /><br /> We&rsquo;ll notify you when your order is on its way! If you have any questions, feel free to reach out to us. <br /><br /> Thank you for trusting [[ company_name ]] for your pet care needs!<br /><br /> Best regards,<br />The [[ company_name ]] Team <br />Contact us: [[ company_contact_info ]]</p>
",
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'status' => 1,
            'user_type' => 'admin',
            'mail_subject' => 'Pet Care Product Order Accepted',
            'mail_template_detail' => '<p>An order for pet care products has been accepted. Please review the order details and ensure processing.</p>',
            'subject' => 'Pet Care Product Order Accepted',
            'template_detail' => '<p>Dear [[ admin_name ]], <br /><br /> The order for pet care products has been accepted and is now in progress. <br /><br />Please find the order details below: <br />Customer Name: [[ user_name ]] <br /><br /> Please make sure the fulfillment process continues smoothly.<br /><br /> Best regards, <br /> The [[ company_name ]] Team <br /> Contact: [[ company_contact_info ]]</p>
<p><span style="font-family: Arial; font-size: 13px; white-space-collapse: preserve;">&nbsp;</span></p>',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'status' => 1,
            'user_type' => 'demo_admin',
            'mail_subject' => 'Pet Care Product Order Accepted',
            'mail_template_detail' => '<p>An order for pet care products has been accepted. Please review the order details and ensure processing.</p>',
            'subject' => 'Pet Care Product Order Accepted',
            'template_detail' => '<p>Dear [[ admin_name ]], <br /><br /> The order for pet care products has been accepted and is now in progress. <br /><br />Please find the order details below: <br />Customer Name: [[ user_name ]] <br /><br /> Please make sure the fulfillment process continues smoothly.<br /><br /> Best regards, <br /> The [[ company_name ]] Team <br /> Contact: [[ company_contact_info ]]</p>
<p><span style="font-family: Arial; font-size: 13px; white-space-collapse: preserve;">&nbsp;</span></p>',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'order_proccessing',
            'name' => 'order_proccessing',
            'label' => 'Order Processing',
            'status' => 1,
            'to' => '["user","admin","demo_admin"]',
            'channels' => ['IS_MAIL' => '0','PUSH_NOTIFICATION' => '1','IS_CUSTOM_WEBHOOK' => '0'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'status' => 1,
            'user_type' => 'user',
            'mail_subject' => 'Your Product Order is Being Processed',
            'mail_template_detail' => '<p>The pet care product order is now being processed. Please review and ensure fulfillment.</p>
',
            'subject' => 'Your Product Order is Being Processed',
            'template_detail' => "<p>Dear [[ user_name ]], <br />|<br />We are excited to let you know that your order for pet care products is now being processed! <br /><br />Our team is getting everything ready to ship your items as quickly as possible. <br /><br /> We will notify you as soon as your order ships. If you have any questions or need assistance, don't hesitate to contact us.<br /><br /> Thank you for shopping with [[ company_name ]]! <br /><br /> Best regards, <br /> The [[ company_name ]] Team<br /> Contact us: [[ company_contact_info ]]</p>",
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
             'status' => 1,
            'user_type' => 'admin',
            'mail_subject' => 'Product Order - Now Being Processed',
            'mail_template_detail' => '<p>Your order for pet care products is now being processed! We&rsquo;ll update you once it&rsquo;s on the way.</p>',
            'subject' => 'Pet Care Product Order Now Being Processed',
            'template_detail' => '<p>Dear [[ admin_name ]],<br /><br /> The order for pet care products is now being processed.<br /><br /> Below are the details for your review: Customer Name: [[ user_name ]] <br /><br /> Please ensure the processing steps continue smoothly and take the necessary actions for fulfillment. <br /><br /> Best regards, <br /> The [[ company_name ]] Team<br /> Contact: [[ company_contact_info ]]</p>
<p><span style="font-family: Arial; font-size: 13px; white-space-collapse: preserve;">&nbsp;</span></p>',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => "A new order is being processed. View the details in the demo admin panel.",
            'status' => 1,
            'user_type' => 'demo_admin',
            'mail_subject' => 'Product Order - Now Being Processed',
            'mail_template_detail' => '<p>Your order for pet care products is now being processed! We&rsquo;ll update you once it&rsquo;s on the way.</p>',
            'subject' => 'Pet Care Product Order Now Being Processed',
            'template_detail' => '<p>Dear [[ admin_name ]],<br /><br /> The order for pet care products is now being processed.<br /><br /> Below are the details for your review: Customer Name: [[ user_name ]] <br /><br /> Please ensure the processing steps continue smoothly and take the necessary actions for fulfillment. <br /><br /> Best regards, <br /> The [[ company_name ]] Team<br /> Contact: [[ company_contact_info ]]</p>
<p><span style="font-family: Arial; font-size: 13px; white-space-collapse: preserve;">&nbsp;</span></p>',
        ]);


        $template = NotificationTemplate::create([
            'type' => 'order_delivered',
            'name' => 'order_delivered',
            'label' => 'Order Delivered',
            'status' => 1,
            'to' => '["user","admin","demo_admin"]',
            'channels' => ['IS_MAIL' => '0','PUSH_NOTIFICATION' => '1','IS_CUSTOM_WEBHOOK' => '0'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'status' => 1,
            'user_type' => 'user',
            'mail_subject' => 'Order Delivered - Confirmation for Pet Care Products',
            'mail_template_detail' => '<p>The order for pet care products has been delivered to the customer. Please update the records.</p>',
            'subject' => 'Order Delivered - Confirmation for Pet Care Products',
            'template_detail' => "<p>Dear [[ admin_name ]],<br /> <br /> The order for pet care products has been successfully delivered to the customer.&nbsp;<br /><br />Here are the details: <br />Customer Name: [[ user_name ]] <br /><br /> Please update the records accordingly.<br /> <br />If there are any issues, please address them promptly. <br /><br /> Best regards, <br /> The [[ company_name ]] Team<br /> Contact: [[ company_contact_info ]]</p>
",
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'status' => 1,
            'user_type' => 'admin',
            'mail_subject' => 'Order Delivered - Confirmation for Pet Care Products',
            'mail_template_detail' => '<p><span style="font-family: Arial; font-size: 13px; white-space-collapse: preserve;">The order for pet care products has been delivered to the customer. Please update the records.</span></p>',
            'subject' => 'Order Delivered - Confirmation for Pet Care Products',
            'template_detail' => '<p>Dear [[ admin_name ]], <br /><br /> The order for pet care products has been successfully delivered to the customer. <br /><br />Here are the details: <br />Customer Name: [[ user_name ]] <br /><br /> Please update the records accordingly. If there are any issues, please address them promptly. <br /><br /> Best regards, <br /> The [[ company_name ]] Team<br /> Contact: [[ company_contact_info ]]</p>
<p><span style="font-family: Arial; font-size: 13px; white-space-collapse: preserve;">&nbsp;</span></p>',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',

            'status' => 1,
            'user_type' => 'demo_admin',
            'mail_subject' => 'Order Delivered - Confirmation for Pet Care Products',
            'mail_template_detail' => '<p><span style="font-family: Arial; font-size: 13px; white-space-collapse: preserve;">The order for pet care products has been delivered to the customer. Please update the records.</span></p>',
            'subject' => 'Order Delivered - Confirmation for Pet Care Products',
            'template_detail' => '<p>Dear [[ admin_name ]], <br /><br /> The order for pet care products has been successfully delivered to the customer. <br /><br />Here are the details: <br />Customer Name: [[ user_name ]] <br /><br /> Please update the records accordingly. If there are any issues, please address them promptly. <br /><br /> Best regards, <br /> The [[ company_name ]] Team<br /> Contact: [[ company_contact_info ]]</p>
<p><span style="font-family: Arial; font-size: 13px; white-space-collapse: preserve;">&nbsp;</span></p>',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'order_cancelled',
            'name' => 'order_cancelled',
            'label' => 'Oreder Cancelled',
            'status' => 1,
            'to' => '["user","admin","demo_admin"]',
            'channels' => ['IS_MAIL' => '0','PUSH_NOTIFICATION' => '1','IS_CUSTOM_WEBHOOK' => '0'],
        ]);

    $template->defaultNotificationTemplateMap()->create([
        'language' => 'en',
        'status' => 1,
        'user_type' => 'user',
        'mail_subject' => 'Your Product Order Has Been Cancelled',
        'mail_template_detail' => "<p>We're sorry to inform you that your order has been cancelled. Please contact us if you need further assistance.</p>",
        'subject' => 'Your Product Order Has Been Cancelled',
        'template_detail' => '<p>Dear [[ user_name ]], <br /> We regret to inform you that your order has been cancelled. <br /><br /> We apologize for any inconvenience this may have caused. <br /><br />If you have any questions or would like assistance with placing a new order, please do not hesitate to reach out to us. <br /><br /> Thank you for understanding. <br /><br /> Best regards, <br /> The [[ company_name ]] Team <br />Contact us: [[ company_contact_info ]]</p>
<p><span style="font-family: Arial; font-size: 13px; white-space-collapse: preserve;">&nbsp;</span></p>',
    ]);

    $template->defaultNotificationTemplateMap()->create([
        'language' => 'en',
        'status' => 1,
        'user_type' => 'admin',
        'mail_subject' => 'Order Cancelled',
        'mail_template_detail' => '<p>An order has been cancelled by the [[ user_name ]]. Please update the records and take necessary actions.</p>',
        'subject' => 'Order Cancelled',
        'template_detail' => "<p>Dear [[ admin_name ]], <br /><br /> An order has been cancelled. <br />Below are the details: <br />Customer Name: [[ user_name ]] <br /><br /> Please review the cancellation and update the records accordingly. <br />If further action is needed, please address it promptly. <br /><br /> Best regards, <br /> The [[ company_name ]] Team <br /> Contact: [[ company_contact_info ]]</p>
",
    ]);

    $template->defaultNotificationTemplateMap()->create([
        'language' => 'en',
        'status' => 1,
        'user_type' => 'demo_admin',
        'mail_subject' => 'Order Cancelled',
        'mail_template_detail' => '<p>An order has been cancelled by the [[ user_name ]]. Please update the records and take necessary actions.</p>',
        'subject' => 'Order Cancelled',
        'template_detail' => "<p>Dear [[ admin_name ]], <br /><br /> An order has been cancelled. <br />Below are the details: <br />Customer Name: [[ user_name ]] <br /><br /> Please review the cancellation and update the records accordingly. <br />If further action is needed, please address it promptly. <br /><br /> Best regards, <br /> The [[ company_name ]] Team <br /> Contact: [[ company_contact_info ]]</p>
",
    ]);

    }
}
