<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('user_notification_preferences')) {
            Schema::create('user_notification_preferences', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                // Canais: email, whatsapp, desktop, slack, team_wide
                $table->json('channels')->nullable()->comment('{"email":true,"whatsapp":false,"desktop":false,"slack":false,"team_wide":false}');
                // Outras notificações: task_alert, budget_warning, invoice_alert, etc.
                $table->json('other')->nullable()->comment('{"task_alert":true,"budget_warning":true,"invoice_alert":false,"feedback_alert":true,"collaboration_request":true,"meeting_reminder":false,"status_change":true}');
                $table->timestamps();

                $table->unique('user_id');
            });
        } else {
            Schema::table('user_notification_preferences', function (Blueprint $table) {
                if (!Schema::hasColumn('user_notification_preferences', 'channels')) {
                    $table->json('channels')->nullable()->after('user_id');
                }
                if (!Schema::hasColumn('user_notification_preferences', 'other')) {
                    $table->json('other')->nullable()->after('channels');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notification_preferences');
    }
};
