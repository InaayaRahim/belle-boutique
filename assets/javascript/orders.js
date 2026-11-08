// Get the search box, status filter and order table
const orderSearch = document.getElementById("orderSearch");
const statusFilter = document.getElementById("statusFilter");
const orderTable = document.querySelector(".table-container table");

// Filter orders whenever the user types or changes status
function filterOrders() {

    const searchValue = orderSearch.value.toLowerCase();
    const selectedStatus = statusFilter.value.toLowerCase();

    const rows = orderTable.querySelectorAll("tbody tr");

    rows.forEach(row => {

        const rowText = row.textContent.toLowerCase();

        const statusCell = row.cells[7];
        const orderStatus = statusCell
            ? statusCell.textContent.trim().toLowerCase()
            : "";

        const matchesSearch = rowText.includes(searchValue);

        const matchesStatus =
            selectedStatus === "" || orderStatus === selectedStatus;

        row.style.display =
            matchesSearch && matchesStatus ? "" : "none";
    });
}

// Listen for user input
orderSearch.addEventListener("input", filterOrders);
statusFilter.addEventListener("change", filterOrders);