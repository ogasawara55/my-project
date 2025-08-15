<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Company;
use App\Models\JobSeeker;
use App\Models\Job;
use App\Models\Application;
use App\Models\Bookmark;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 企業データ作成（個別に作成）
        $company1 = Company::create([
            'company_name' => 'テクノロジー株式会社',
            'contact_name' => '田中太郎',
            'email' => 'tanaka@technology.co.jp',
            'password' => Hash::make('password123'),
        ]);

        $company2 = Company::create([
            'company_name' => 'イノベーション株式会社', 
            'contact_name' => '佐藤花子',
            'email' => 'sato@innovation.co.jp',
            'password' => Hash::make('password123'),
        ]);

        $company3 = Company::create([
            'company_name' => 'スタートアップ合同会社',
            'contact_name' => '山田次郎',
            'email' => 'yamada@startup.com',
            'password' => Hash::make('password123'),
        ]);

        // 求職者データ作成（個別に作成）
        $seeker1 = JobSeeker::create([
            'name' => '鈴木一郎',
            'email' => 'suzuki@example.com',
            'password' => Hash::make('password123'),
            'phone' => '090-1234-5678',
            'career' => 'Webエンジニア歴3年',
            'self_pr' => 'PHP、Laravel開発経験があります',
        ]);

        $seeker2 = JobSeeker::create([
            'name' => '高橋美咲',
            'email' => 'takahashi@example.com', 
            'password' => Hash::make('password123'),
            'phone' => '080-9876-5432',
            'career' => 'フロントエンド開発歴2年',
            'self_pr' => 'React、Vue.js使用経験があります',
        ]);

        $seeker3 = JobSeeker::create([
            'name' => '伊藤健太',
            'email' => 'ito@example.com',
            'password' => Hash::make('password123'),
            'phone' => '070-1111-2222',
            'career' => 'インフラエンジニア歴5年',
            'self_pr' => 'AWS、Docker使用経験があります',
        ]);

        $seeker4 = JobSeeker::create([
            'name' => '渡辺さくら',
            'email' => 'watanabe@example.com',
            'password' => Hash::make('password123'),
            'phone' => '090-3333-4444',
            'career' => 'デザイナー歴4年',
            'self_pr' => 'UI/UXデザイン得意です',
        ]);

        // 求人データ作成（個別に作成）
        $job1 = Job::create([
            'company_id' => $company1->id,
            'title' => 'Webエンジニア（Laravel）',
            'description' => 'LaravelでのWebアプリケーション開発をお任せします。',
            'location' => '東京都渋谷区',
            'salary_range' => '400-600万円',
            'employment_type' => '正社員',
            'image_url' => null,
        ]);

        $job2 = Job::create([
            'company_id' => $company1->id,
            'title' => 'フロントエンドエンジニア',
            'description' => 'React/Vue.jsを使ったフロントエンド開発',
            'location' => '東京都新宿区', 
            'salary_range' => '350-550万円',
            'employment_type' => '正社員',
            'image_url' => null,
        ]);

        $job3 = Job::create([
            'company_id' => $company2->id,
            'title' => 'データサイエンティスト',
            'description' => '機械学習・AI技術を活用したデータ分析',
            'location' => '東京都港区',
            'salary_range' => '500-800万円', 
            'employment_type' => '正社員',
            'image_url' => null,
        ]);

        $job4 = Job::create([
            'company_id' => $company2->id,
            'title' => 'プロダクトマネージャー',
            'description' => 'Webサービスの企画・開発ディレクション',
            'location' => '東京都品川区',
            'salary_range' => '600-900万円',
            'employment_type' => '正社員', 
            'image_url' => null,
        ]);

        $job5 = Job::create([
            'company_id' => $company3->id,
            'title' => 'リードエンジニア',
            'description' => 'スタートアップでの技術リード',
            'location' => '東京都中央区',
            'salary_range' => '700-1000万円',
            'employment_type' => '正社員',
            'image_url' => null,
        ]);

        $job6 = Job::create([
            'company_id' => $company3->id,
            'title' => '業務委託 Webデザイナー',
            'description' => 'Webサイト・アプリのUI/UXデザイン',
            'location' => 'リモート可',
            'salary_range' => '時給3000-5000円',
            'employment_type' => '業務委託',
            'image_url' => null,
        ]);

        $job7 = Job::create([
            'company_id' => $company1->id,
            'title' => 'インフラエンジニア',
            'description' => 'AWS環境での運用・保守業務',
            'location' => '東京都千代田区',
            'salary_range' => '450-650万円',
            'employment_type' => '正社員',
            'image_url' => null,
        ]);

        // 応募データ作成（個別に作成）
        Application::create([
            'job_id' => $job1->id,
            'seeker_id' => $seeker1->id,
            'motivation' => 'Laravel開発経験を活かしたいです',
            'email' => 'suzuki@example.com',
            'phone' => '090-1234-5678',
            'status' => Application::STATUS_APPLIED,
        ]);

        Application::create([
            'job_id' => $job2->id,
            'seeker_id' => $seeker2->id,
            'motivation' => 'フロントエンド開発が得意です',
            'email' => 'takahashi@example.com',
            'phone' => '080-9876-5432', 
            'status' => Application::STATUS_SCREENING,
        ]);

        Application::create([
            'job_id' => $job7->id,
            'seeker_id' => $seeker3->id,
            'motivation' => 'インフラ運用の経験を活かしたいです',
            'email' => 'ito@example.com',
            'phone' => '070-1111-2222',
            'status' => Application::STATUS_NOTIFIED,
        ]);

        Application::create([
            'job_id' => $job6->id,
            'seeker_id' => $seeker4->id,
            'motivation' => 'UI/UXデザインが得意です',
            'email' => 'watanabe@example.com',
            'phone' => '090-3333-4444',
            'status' => Application::STATUS_APPLIED,
        ]);

        // ブックマークデータ作成（個別に作成）
        Bookmark::create(['seeker_id' => $seeker1->id, 'job_id' => $job2->id]);
        Bookmark::create(['seeker_id' => $seeker1->id, 'job_id' => $job3->id]);
        Bookmark::create(['seeker_id' => $seeker2->id, 'job_id' => $job1->id]);
        Bookmark::create(['seeker_id' => $seeker2->id, 'job_id' => $job4->id]);
        Bookmark::create(['seeker_id' => $seeker3->id, 'job_id' => $job5->id]);
        Bookmark::create(['seeker_id' => $seeker4->id, 'job_id' => $job1->id]);
        Bookmark::create(['seeker_id' => $seeker4->id, 'job_id' => $job3->id]);

        echo "テストデータの作成が完了しました！\n";
        echo "企業: " . Company::count() . "件\n";
        echo "求職者: " . JobSeeker::count() . "件\n"; 
        echo "求人: " . Job::count() . "件\n";
        echo "応募: " . Application::count() . "件\n";
        echo "ブックマーク: " . Bookmark::count() . "件\n";
    }
}