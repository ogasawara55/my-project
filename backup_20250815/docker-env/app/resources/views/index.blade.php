<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>求人応募ポータル</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }
        
        .main-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }
        
        .content-wrapper {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .header-section {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .header-section h1 {
            color: #343a40;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .header-section p {
            color: #6c757d;
            font-size: 1.1rem;
        }
        
        .choice-card {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .choice-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .card-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .jobseeker-icon {
            color: #007bff;
        }
        
        .company-icon {
            color: #28a745;
        }
        
        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #343a40;
        }
        
        .card-description {
            color: #6c757d;
            margin-bottom: 2rem;
        }
        
        .btn-group-vertical .btn {
            margin-bottom: 0.5rem;
        }
        
        .btn-group-vertical .btn:last-child {
            margin-bottom: 0;
        }
        
        /* 中央寄せ強化 */
        .row {
            justify-content: center;
        }
        
        @media (max-width: 768px) {
            .content-wrapper {
                padding: 0 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-10 col-lg-8">
                <!-- ヘッダーセクション -->
                <div class="header-section">
                    <h1><i class="fas fa-briefcase me-2"></i>求人応募ポータル</h1>
                    <p>企業と求職者をつなぐ採用プラットフォーム</p>
                </div>
                
                <!-- 選択カード -->
                <div class="row">
                    <!-- 求職者向けカード -->
                    <div class="col-md-6">
                        <div class="choice-card">
                            <div class="card-icon jobseeker-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <h3 class="card-title">求職者の方</h3>
                            <p class="card-description">
                                理想のキャリアを見つけて、新しい挑戦を始めましょう
                            </p>
                            
                            <div class="btn-group-vertical d-grid gap-2">
                                <a href="/job_seeker/login" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt me-2"></i>ログイン
                                </a>
                                <a href="/job_seeker/register" class="btn btn-outline-primary">
                                    <i class="fas fa-user-plus me-2"></i>新規登録
                                </a>
                                <a href="/jobs" class="btn btn-outline-secondary">
                                    <i class="fas fa-eye me-2"></i>ログインせずに求人を見る
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 企業向けカード -->
                    <div class="col-md-6">
                        <div class="choice-card">
                            <div class="card-icon company-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <h3 class="card-title">企業の方</h3>
                            <p class="card-description">
                                優秀な人材を発見し、効率的な採用活動を実現しましょう
                            </p>
                            
                            <div class="btn-group-vertical d-grid gap-2">
                                <a href="/company/login" class="btn btn-success">
                                    <i class="fas fa-sign-in-alt me-2"></i>ログイン
                                </a>
                                <a href="/company/register" class="btn btn-outline-success">
                                    <i class="fas fa-building me-2"></i>新規登録
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>