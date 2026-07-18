

import Alpine from 'alpinejs';
import Swal from 'sweetalert2';
import Chart from 'chart.js/auto';
import { DataTable, exportCSV } from 'simple-datatables';
import 'simple-datatables/dist/style.css';

window.Alpine = Alpine;
window.Swal = Swal;
window.Chart = Chart;

const initializeDataTables = () => {
    document.querySelectorAll('main table:not([data-table-static])').forEach((table, index) => {
        if (table.dataset.tableReady === 'true' || !table.querySelector('thead')) return;

        table.dataset.tableReady = 'true';
        const title = document.querySelector('main h1, main h2')?.textContent?.trim() || `tabel-${index + 1}`;
        const filename = title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '') || `tabel-${index + 1}`;
        const headings = [...(table.querySelector('thead tr')?.children || [])];
        const lastColumn = headings.length - 1;
        const actionColumn = lastColumn >= 0 && headings[lastColumn].textContent.trim() === '' ? lastColumn : null;
        const dataTable = new DataTable(table, {
            perPage: 10,
            perPageSelect: [10, 25, 50, 100],
            searchable: true,
            sortable: true,
            columns: actionColumn !== null ? [{ select: actionColumn, sortable: false, searchable: false }] : [],
            labels: {
                placeholder: 'Cari dalam tabel...',
                searchTitle: 'Pencarian tabel',
                perPage: 'baris per halaman',
                noRows: 'Data tidak tersedia',
                noResults: 'Data tidak ditemukan',
                info: 'Menampilkan {start}–{end} dari {rows} baris',
            },
        });

        const toolbar = document.createElement('div');
        toolbar.className = 'table-action-toolbar';
        toolbar.innerHTML = '<span class="table-action-label">Aksi tabel</span>';

        const printButton = document.createElement('button');
        printButton.type = 'button';
        printButton.className = 'table-action-button';
        printButton.textContent = 'Cetak';
        printButton.addEventListener('click', () => dataTable.print());

        const exportButton = document.createElement('button');
        exportButton.type = 'button';
        exportButton.className = 'table-action-button table-action-button-primary';
        exportButton.textContent = 'Export CSV';
        exportButton.addEventListener('click', () => exportCSV(dataTable, {
            filename,
            skipColumn: actionColumn !== null ? [actionColumn] : [],
        }));

        toolbar.append(printButton, exportButton);
        dataTable.wrapperDOM.insertBefore(toolbar, dataTable.wrapperDOM.firstChild);
    });
};

document.addEventListener('DOMContentLoaded', initializeDataTables);

Alpine.start();
