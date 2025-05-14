@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- Add Translation --}}
    <div class="card mx-auto shadow-sm mb-5" style="max-width: 500px;">
        <div class="card-body">
            <h4 class="card-title text-center mb-4">Add Translation</h4>
            <form id="add-translation-form">
                <input type="text" class="form-control mb-3" name="locale" placeholder="Locale (e.g. en, fr)" required>
                <input type="text" class="form-control mb-3" name="key" placeholder="Key (e.g. welcome_message)" required>
                <input type="text" class="form-control mb-3" name="content" placeholder="Content (e.g. Welcome!)" required>
                <input type="text" class="form-control mb-3" name="tag" placeholder="Tag (optional)">
                <button type="submit" class="btn btn-primary w-100">Save</button>
            </form>
        </div>
    </div>

    {{-- Search --}}
    <h3 class="text-center mb-4">Translations</h3>
    <div class="mb-4">
        <input type="text" class="form-control" id="search-input" placeholder="Search by key or content...">
    </div>

    {{-- Pagination Top --}}
    <div id="pagination-top" class="d-flex justify-content-center mb-3 flex-wrap gap-1"></div>

    {{-- Translations Grid --}}
    <div id="translations-list" class="row g-3 mb-3"></div>
    <div id="no-results" class="text-center text-muted d-none">No translations found.</div>

    {{-- Pagination Bottom --}}
    <div id="pagination-bottom" class="d-flex justify-content-center mt-3 flex-wrap gap-1"></div>

    {{-- Export Translations --}}
    <div class="card mx-auto shadow-sm mt-5" style="max-width: 600px;">
        <div class="card-body">
            <h4 class="card-title text-center mb-3">Export Translations</h4>
            <form id="export-form" class="d-flex gap-2 mb-3">
                <input type="text" class="form-control" name="locale" placeholder="Locale (e.g., en)" required>
                <button type="submit" class="btn btn-primary">Export</button>
            </form>
            <div id="export-result" class="bg-light p-3 border rounded d-none" style="max-height: 400px; overflow-y: auto;"></div>
            <div id="export-empty" class="text-muted text-center">No data exported yet.</div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('add-translation-form');
        const list = document.getElementById('translations-list');
        const searchInput = document.getElementById('search-input');
        const noResults = document.getElementById('no-results');
        const exportForm = document.getElementById('export-form');
        const exportResult = document.getElementById('export-result');
        const exportEmpty = document.getElementById('export-empty');
        const paginationTop = document.getElementById('pagination-top');
        const paginationBottom = document.getElementById('pagination-bottom');

        let currentPage = 1;
        let currentSearch = '';
        let pageGroupStart = 1;

        const fetchTranslations = async (query = '', page = 1) => {
            currentPage = page;
            const res = await fetch(`/translations?search=${encodeURIComponent(query)}&page=${page}`);
            const {
                data,
                total,
                current_page,
                last_page
            } = await res.json();

            list.innerHTML = '';
            paginationTop.innerHTML = '';
            paginationBottom.innerHTML = '';

            if (data.length) {
                data.forEach(t => {
                    const col = document.createElement('div');
                    col.className = 'col-md-4';
                    col.innerHTML = `
                    <div class="list-group-item list-group-item-action shadow-sm mb-2 rounded">
                        <div>
                            <strong>${t.locale}</strong> — <span class="text-muted">${t.key}</span>
                            <div class="small">${t.content}</div>
                        </div>
                    </div>
                `;
                    list.appendChild(col);
                });
                list.classList.remove('d-none');
                noResults.classList.add('d-none');
            } else {
                noResults.classList.remove('d-none');
            }

            renderPagination(paginationTop, last_page, current_page);
            renderPagination(paginationBottom, last_page, current_page);
        };

        const renderPagination = (container, totalPages, currentPage) => {
            container.innerHTML = '';

            const createBtn = (label, targetPage, disabled = false, active = false) => {
                const btn = document.createElement('button');
                btn.textContent = label;
                btn.className = `btn btn-sm ${active ? 'btn-primary' : 'btn-outline-primary'} mx-1`;
                btn.disabled = disabled;
                if (!disabled) {
                    btn.addEventListener('click', () => {
                        if (label === '«') {
                            pageGroupStart = Math.max(1, pageGroupStart - 25);
                            fetchTranslations(currentSearch, pageGroupStart);
                        } else if (label === '»') {
                            pageGroupStart = Math.min(totalPages - 24, pageGroupStart + 25);
                            fetchTranslations(currentSearch, pageGroupStart);
                        } else {
                            if (targetPage < pageGroupStart || targetPage > pageGroupStart + 24) {
                                pageGroupStart = Math.floor((targetPage - 1) / 25) * 25 + 1;
                            }
                            fetchTranslations(currentSearch, targetPage);
                        }
                    });
                }
                container.appendChild(btn);
            };

            // Arrows
            createBtn('«', pageGroupStart - 1, pageGroupStart === 1);

            const groupEnd = Math.min(pageGroupStart + 24, totalPages);
            for (let i = pageGroupStart; i <= groupEnd; i++) {
                createBtn(i, i, false, i === currentPage);
            }

            createBtn('»', groupEnd + 1, groupEnd >= totalPages);
        };

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            const payload = Object.fromEntries(formData.entries());

            await fetch('/api/translations', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            form.reset();
            fetchTranslations(currentSearch, currentPage);
        });

        searchInput.addEventListener('input', (e) => {
            currentSearch = e.target.value;
            pageGroupStart = 1;
            fetchTranslations(currentSearch, 1);
        });

        exportForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const locale = exportForm.querySelector('input[name="locale"]').value;
            try {
                const res = await fetch(`/translations/export/${locale}`);
                const json = await res.json();
                exportResult.textContent = JSON.stringify(json, null, 2);
                exportResult.classList.remove('d-none');
                exportEmpty.classList.add('d-none');
            } catch (err) {
                exportResult.textContent = '// Failed to export';
                exportResult.classList.remove('d-none');
            }
        });

        fetchTranslations();
    });
</script>

@endpush