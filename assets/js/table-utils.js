/**
 * ========================================
 * TABLE UTILITIES
 * ========================================
 * File: assets/js/table-utils.js
 * Fungsi: Reusable functions untuk search dan sort table
 */

/**
 * Initialize table search functionality
 * @param {string} tableId - ID of the table element
 * @param {string} searchInputId - ID of the search input element
 */
function initTableSearch(tableId, searchInputId) {
    const searchInput = document.getElementById(searchInputId);
    const table = document.getElementById(tableId);

    if (!searchInput || !table) {
        console.error('Table or search input not found');
        return;
    }

    const tbody = table.querySelector('tbody');

    searchInput.addEventListener('keyup', function () {
        const searchTerm = this.value.toLowerCase().trim();
        const rows = tbody.querySelectorAll('tr');
        let visibleCount = 0;

        rows.forEach(row => {
            // Skip if it's a "no data" row
            if (row.classList.contains('no-data-row')) {
                return;
            }

            const text = row.textContent.toLowerCase();

            if (text.includes(searchTerm)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Show/hide "no results" message
        updateNoResultsMessage(tbody, visibleCount);
    });
}

/**
 * Update or create "no results" message
 */
function updateNoResultsMessage(tbody, visibleCount) {
    const existingMsg = tbody.querySelector('.no-results-row');

    if (visibleCount === 0) {
        if (!existingMsg) {
            const colCount = tbody.closest('table').querySelectorAll('thead th').length;
            const noResultsRow = document.createElement('tr');
            noResultsRow.className = 'no-results-row';
            noResultsRow.innerHTML = `
                <td colspan="${colCount}" class="text-center py-4">
                    <i class="fas fa-search text-muted mb-2" style="font-size: 2rem;"></i>
                    <p class="text-muted mb-0">Tidak ada data yang cocok dengan pencarian</p>
                </td>
            `;
            tbody.appendChild(noResultsRow);
        }
    } else {
        if (existingMsg) {
            existingMsg.remove();
        }
    }
}

/**
 * Initialize sortable columns
 * @param {string} tableId - ID of the table element
 */
function initTableSort(tableId) {
    const table = document.getElementById(tableId);

    if (!table) {
        console.error('Table not found');
        return;
    }

    const headers = table.querySelectorAll('th.sortable');

    headers.forEach((header, index) => {
        header.style.cursor = 'pointer';
        header.style.userSelect = 'none';

        // Add sort icon if not exists
        if (!header.querySelector('.sort-icon')) {
            const icon = document.createElement('i');
            icon.className = 'fas fa-sort sort-icon ms-1';
            icon.style.fontSize = '0.8em';
            icon.style.opacity = '0.5';
            header.appendChild(icon);
        }

        header.addEventListener('click', function () {
            sortTable(table, index, this);
        });
    });
}

/**
 * Sort table by column
 */
function sortTable(table, columnIndex, header) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr')).filter(row =>
        !row.classList.contains('no-results-row') &&
        !row.classList.contains('no-data-row')
    );

    // Determine sort direction
    const currentDirection = header.dataset.sortDirection || 'asc';
    const newDirection = currentDirection === 'asc' ? 'desc' : 'asc';

    // Reset all other headers
    table.querySelectorAll('th.sortable').forEach(th => {
        const icon = th.querySelector('.sort-icon');
        if (th !== header && icon) {
            icon.className = 'fas fa-sort sort-icon ms-1';
            icon.style.opacity = '0.5';
            delete th.dataset.sortDirection;
        }
    });

    // Update current header
    header.dataset.sortDirection = newDirection;
    const icon = header.querySelector('.sort-icon');
    if (icon) {
        icon.className = `fas fa-sort-${newDirection === 'asc' ? 'up' : 'down'} sort-icon ms-1`;
        icon.style.opacity = '1';
        icon.style.color = '#0d6efd';
    }

    // Sort rows
    rows.sort((a, b) => {
        const aCell = a.cells[columnIndex];
        const bCell = b.cells[columnIndex];

        if (!aCell || !bCell) return 0;

        const aValue = getCellValue(aCell);
        const bValue = getCellValue(bCell);

        return compareValues(aValue, bValue, newDirection);
    });

    // Re-append rows in sorted order
    rows.forEach(row => tbody.appendChild(row));
}

/**
 * Get cell value for sorting
 */
function getCellValue(cell) {
    // Check for data-sort attribute first
    if (cell.hasAttribute('data-sort')) {
        return cell.getAttribute('data-sort');
    }

    // Get text content, removing extra whitespace
    return cell.textContent.trim();
}

/**
 * Compare two values for sorting
 */
function compareValues(a, b, direction) {
    // Try to parse as numbers
    const aNum = parseFloat(a.replace(/[^\d.-]/g, ''));
    const bNum = parseFloat(b.replace(/[^\d.-]/g, ''));

    let result;

    if (!isNaN(aNum) && !isNaN(bNum)) {
        // Numeric comparison
        result = aNum - bNum;
    } else {
        // String comparison (case-insensitive)
        result = a.toLowerCase().localeCompare(b.toLowerCase());
    }

    return direction === 'asc' ? result : -result;
}

/**
 * Initialize both search and sort for a table
 * @param {string} tableId - ID of the table element
 * @param {string} searchInputId - ID of the search input element
 */
function initTable(tableId, searchInputId) {
    initTableSearch(tableId, searchInputId);
    initTableSort(tableId);
}
