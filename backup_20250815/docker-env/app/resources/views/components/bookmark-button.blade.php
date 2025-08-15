<!-- ブックマークボタンコンポーネント -->
<!-- 求人詳細ページなどで使用 -->
<button class="btn bookmark-btn" 
        data-job-id="{{ $jobId }}" 
        data-bookmarked="{{ $isBookmarked ? 'true' : 'false' }}">
    <i class="fas fa-bookmark me-2"></i>
    <span class="bookmark-text">
        {{ $isBookmarked ? 'ブックマーク済み' : 'ブックマーク' }}
    </span>
</button>

<style>
.bookmark-btn {
    transition: all 0.3s ease;
}

.bookmark-btn[data-bookmarked="false"] {
    background-color: #ffffff;
    color: #007bff;
    border: 2px solid #007bff;
}

.bookmark-btn[data-bookmarked="false"]:hover {
    background-color: #007bff;
    color: #ffffff;
}

.bookmark-btn[data-bookmarked="true"] {
    background-color: #007bff;
    color: #ffffff;
    border: 2px solid #007bff;
}

.bookmark-btn[data-bookmarked="true"]:hover {
    background-color: #dc3545;
    border-color: #dc3545;
}

.bookmark-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ブックマークボタンのクリックイベント
    document.querySelectorAll('.bookmark-btn').forEach(button => {
        button.addEventListener('click', function() {
            const jobId = this.dataset.jobId;
            const isBookmarked = this.dataset.bookmarked === 'true';
            const bookmarkText = this.querySelector('.bookmark-text');
            const icon = this.querySelector('i');
            
            // ボタンを無効化（連続クリック防止）
            this.disabled = true;
            
            // ローディング表示
            const originalText = bookmarkText.textContent;
            bookmarkText.textContent = '処理中...';
            icon.className = 'fas fa-spinner fa-spin me-2';
            
            // Ajax リクエスト
            const url = isBookmarked ? `/job_seeker/bookmarks/remove/${jobId}` : `/job_seeker/bookmarks/add/${jobId}`;
            const method = isBookmarked ? 'DELETE' : 'POST';
            
            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // ブックマーク状態を切り替え
                    const newBookmarkedState = !isBookmarked;
                    this.dataset.bookmarked = newBookmarkedState.toString();
                    
                    // ボタンの表示を更新
                    if (newBookmarkedState) {
                        bookmarkText.textContent = 'ブックマーク済み';
                        icon.className = 'fas fa-bookmark me-2';
                        showToast('ブックマークに追加しました', 'success');
                    } else {
                        bookmarkText.textContent = 'ブックマーク';
                        icon.className = 'far fa-bookmark me-2';
                        showToast('ブックマークから削除しました', 'info');
                    }
                } else {
                    // エラーの場合は元に戻す
                    bookmarkText.textContent = originalText;
                    icon.className = 'fas fa-bookmark me-2';
                    showToast(data.message || 'エラーが発生しました', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                bookmarkText.textContent = originalText;
                icon.className = 'fas fa-bookmark me-2';
                showToast('通信エラーが発生しました', 'error');
            })
            .finally(() => {
                // ボタンを再有効化
                this.disabled = false;
            });
        });
    });
});

// トースト通知を表示する関数
function showToast(message, type) {
    // 既存のトーストを削除
    const existingToasts = document.querySelectorAll('.toast-notification');
    existingToasts.forEach(toast => toast.remove());
    
    const toast = document.createElement('div');
    toast.className = `alert alert-${getAlertClass(type)} toast-notification position-fixed`;
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
    toast.style.minWidth = '300px';
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${getIconClass(type)} me-2"></i>
            <span>${message}</span>
            <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // 3秒後に自動削除
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 3000);
}

function getAlertClass(type) {
    switch(type) {
        case 'success': return 'success';
        case 'error': return 'danger';
        case 'info': return 'info';
        default: return 'primary';
    }
}

function getIconClass(type) {
    switch(type) {
        case 'success': return 'check-circle';
        case 'error': return 'exclamation-circle';
        case 'info': return 'info-circle';
        default: return 'bell';
    }
}
</script>