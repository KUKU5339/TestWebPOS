@extends('layout')

@section('content')
<div class="page-header">
    <h2 style="color:#800000; margin:0;" id="settings-title">⚙️ Settings</h2>
    <p style="color:#666; margin:5px 0 0 0;" id="settings-subtitle">Customize your StreetPOS experience</p>
</div>

<div class="card" style="padding:16px;">
    <h3 style="margin-top:0;">Language</h3>
    <p style="color:#666;">Select your preferred language</p>
    <select id="language-select" style="padding:8px; border:1px solid #ccc; border-radius:6px;">
        <option value="en">English</option>
        <option value="tl">Tagalog</option>
    </select>
</div>

<div class="card" style="padding:16px; margin-top:16px;">
    <h3 style="margin-top:0;">Receipts</h3>
    <label style="display:block; margin-bottom:8px;">
        <input type="checkbox" id="receipt-show-thanks" checked>
        Show “Salamat po” message
    </label>
    <label style="display:block; margin-bottom:8px;">
        <input type="checkbox" id="receipt-show-cashier" checked>
        Show cashier name
    </label>
</div>

<div class="card" style="padding:16px; margin-top:16px;">
    <h3 style="margin-top:0;">Notifications</h3>
    <label style="display:block; margin-bottom:8px;">
        <input type="checkbox" id="notif-enable" checked>
        Enable low-stock notifications
    </label>
</div>

<script>
    (function(){
        const langSelect = document.getElementById('language-select');
        const savedLang = localStorage.getItem('streetpos_lang') || 'en';
        langSelect.value = savedLang;
        langSelect.addEventListener('change', function(){
            localStorage.setItem('streetpos_lang', this.value);
            if (typeof window.applyLanguage === 'function') {
                window.applyLanguage(this.value);
            }
            showToast('Language set to ' + (this.value === 'tl' ? 'Tagalog' : 'English'), 'success');
        });

        // Save simple receipt display settings
        function syncSetting(id, key) {
            const el = document.getElementById(id);
            const saved = localStorage.getItem(key);
            if (saved !== null) el.checked = saved === 'true';
            el.addEventListener('change', function(){
                localStorage.setItem(key, this.checked ? 'true' : 'false');
            });
        }
        syncSetting('receipt-show-thanks', 'streetpos_receipt_thanks');
        syncSetting('receipt-show-cashier', 'streetpos_receipt_cashier');
        syncSetting('notif-enable', 'streetpos_notif_enable');
    })();
</script>
@endsection
