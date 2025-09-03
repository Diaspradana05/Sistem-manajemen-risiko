{{-- resources/views/components/helper-probabilitas.blade.php --}}
<style>
/* Popup helper (partial ready to be used inside ->suffix() or @include) */
.helper-popup {
    position: relative;
    display: inline-block;
    vertical-align: middle;
    margin-left: 6px;
}
.helper-icon {
    color: #2563eb;
    font-weight: 700;
    font-size: 16px;
    line-height: 1;
    user-select: none;
}
.helper-content {
    visibility: hidden;
    opacity: 0;
    transition: opacity 0.18s ease;
    width: 420px;
    background: #ffffff;
    color: #111827;
    text-align: left;
    border-radius: 6px;
    padding: 10px;
    position: absolute;
    z-index: 9999;
    top: 26px;
    left: 0;
    border: 1px solid #e5e7eb;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
}
.helper-popup:hover .helper-content,
.helper-popup:focus-within .helper-content {
    visibility: visible;
    opacity: 1;
}

/* table inside helper */
.helper-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
    margin-bottom: 12px;
}
.helper-table th,
.helper-table td {
    border: 1px solid #e5e7eb;
    padding: 6px 8px;
    vertical-align: top;
}
.helper-table th {
    background: #eef6ff;
    text-align: left;
}
.helper-heading {
    display: inline-block;
    padding: 6px 8px;
    border-radius: 4px;
    color: #fff;
    font-weight: 600;
    margin-bottom: 8px;
}
.helper-heading.klinis { background:#2f6fad; }
.helper-heading.nonklinis { background:#1e7a2b; }
</style>

<div class="helper-popup" tabindex="0" aria-label="Helper Probabilitas">
    <span class="helper-icon" aria-hidden="true">ℹ️</span>

    <div class="helper-content" role="dialog" aria-hidden="true">
        <div class="helper-heading klinis">Probabilitas — Resiko Klinis</div>
        <table class="helper-table" aria-label="Probabilitas Resiko Klinis">
            <thead>
                <tr><th style="width:52px">Skor</th><th>Keterangan</th></tr>
            </thead>
            <tbody>
                <tr><td>1</td><td>Sangat Jarang; Dapat terjadi dalam lebih dari 5 tahun</td></tr>
                <tr><td>2</td><td>Jarang; Dapat terjadi dalam 2 – 5 tahun</td></tr>
                <tr><td>3</td><td>Mungkin; Dapat terjadi tiap 1 – 2 tahun</td></tr>
                <tr><td>4</td><td>Sering; Dapat terjadi beberapa kali dalam setahun</td></tr>
                <tr><td>5</td><td>Sangat Sering; Terjadi dalam minggu / bulan</td></tr>
            </tbody>
        </table>

        <div class="helper-heading nonklinis">Probabilitas — Resiko Non Klinis</div>
        <table class="helper-table" aria-label="Probabilitas Resiko Non Klinis">
            <thead>
                <tr><th style="width:52px">Skor</th><th>Keterangan</th></tr>
            </thead>
            <tbody>
                <tr><td>1</td><td>Sangat Jarang; Dapat terjadi dalam lebih dari 5 tahun</td></tr>
                <tr><td>2</td><td>Jarang; Dapat terjadi dalam 2 – 5 tahun</td></tr>
                <tr><td>3</td><td>Mungkin; Dapat terjadi tiap 1 – 2 tahun</td></tr>
                <tr><td>4</td><td>Sering; Dapat terjadi beberapa kali dalam setahun</td></tr>
                <tr><td>5</td><td>Sangat Sering; Terjadi dalam minggu / bulan</td></tr>
            </tbody>
        </table>
    </div>
</div>
