// SWAL FOR LOGOUT
document.addEventListener("DOMContentLoaded", function () {
    const logoutButton = document.getElementById("logout-button");

    if (logoutButton) {
        logoutButton.addEventListener("click", async function (e) {
            e.preventDefault();

            Swal.fire({
                title: "Are you sure?",
                text: "You will be logged out of your session.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#0A9548",
                cancelButtonColor: "#a9a9a9",
                confirmButtonText: "Yes",
                cancelButtonText: "Cancel"
            }).then(async (result) => {
                if (result.isConfirmed) {
                    document.body.innerHTML += '<div class="background-overlay"></div><div class="loader"></div><div class="image-holder"></div>';
                    await new Promise(resolve => setTimeout(resolve, 500));
                    window.location.href = "process/logout.php";
                }
            });
        });
    }
});

// SWAL FOR DELETE BUTTON IN VIEW ACCOUNT FORMS
document.addEventListener("DOMContentLoaded", function () {
    const deleteForms = document.querySelectorAll(".delete-form");

    deleteForms.forEach((form) => {
        const deleteButton = form.querySelector(".delete-button");
        deleteButton.addEventListener("click", function (e) {
            e.preventDefault();

            Swal.fire({
                title: "Are you sure?",
                text: "This action will permanently delete the account.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#0A9548",
                cancelButtonColor: "#a9a9a9",
                confirmButtonText: "Yes",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Successfully Deleted.",
                        icon: "success",
                        confirmButtonText: "Okay"
                    }).then(() => {
                        form.submit();
                    });

                }
            });
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const timeOutButtons = document.querySelectorAll(".time-out-button");

    timeOutButtons.forEach((button) => {
        button.addEventListener("click", function () {
            const visitorCode = this.getAttribute("data-id");
            const form = this.closest(".time-out-form");

            Swal.fire({
                title: "Are you sure?",
                text: `You are about to time out visitor with code: ${visitorCode}.`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#0A9548",
                cancelButtonColor: "#a9a9a9",
                confirmButtonText: "Yes",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});


// Handle "View Details" button clicks
document.addEventListener('DOMContentLoaded', function () {
    document.body.addEventListener('click', function (e) {
        if (e.target.classList.contains('view-details')) {
            const button = e.target;

            // Populate modal with data attributes
            document.getElementById('modal-name').textContent = button.getAttribute('data-name');
            document.getElementById('modal-age').textContent = button.getAttribute('data-age');
            document.getElementById('modal-sex').textContent = button.getAttribute('data-sex');
            document.getElementById('modal-code').textContent = button.getAttribute('data-code');
            document.getElementById('modal-purpose').textContent = button.getAttribute('data-purpose');

            // Set hidden input for printing receipt
            document.getElementById('visitor_code').value = button.getAttribute('data-code');
        }
    });
});


// Pop up modal for edit user account
document.addEventListener("DOMContentLoaded", function () {
    const editButtons = document.querySelectorAll(".editModalBtn");

    editButtons.forEach(button => {
        button.addEventListener("click", function () {
            const id = this.getAttribute("data-id");
            const username = this.getAttribute("data-username");
            const role = this.getAttribute("data-role");

            document.getElementById("edit-id").value = id;
            document.getElementById("edit-username").value = username;
            document.getElementById("edit-role").value = role;
        });
    });
});

//------------------------------------------------------------------//
// START OF FETCH AND UPDATE PAGINATION FOR USER PERMISSION TABLE 

// Fetch user permissions data by search
document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("search-input-user-permission");
    const userPermissionTable = document.getElementById("user-permission-table");

    if (searchInput) {
        searchInput.addEventListener("input", (event) => {
            const query = event.target.value;
            fetch(`fetch-user-permission.php?search=${encodeURIComponent(query)}`)
                .then((response) => {
                    if (!response.ok) throw new Error("Network response was not ok");
                    return response.text();
                })
                .then((data) => {
                    userPermissionTable.innerHTML = data;
                    updatePermissionPagination();
                })
                .catch((error) => console.error("Error fetching data:", error));
        });
    }
});

// Function to update visitor pagination dynamically
function updatePermissionPagination() {
    const query = document.getElementById("search-input-user-permission").value;
    fetch(`fetch-user-permission.php?search=${encodeURIComponent(query)}&pagination=true`)
        .then((response) => {
            if (!response.ok) throw new Error("Network response was not ok");
            return response.text();
        })
        .then((data) => {
            document.getElementById("pagination").innerHTML = data;
            const totalRowsElement = document.getElementById("total-rows");
            if (totalRowsElement) {
                const totalRows = parseInt(totalRowsElement.value);
                const totalPages = Math.ceil(totalRows / 13);
                const currentPage = parseInt(document.getElementById("pagination").querySelector(".active a").textContent);
                document.getElementById("page-info").textContent = `Page ${currentPage} of ${totalPages}`;
            }
        })
        .catch((error) => console.error("Error fetching pagination data:", error));
}

// END OF FETCH AND UPDATE PAGINATION FOR USER PERMISSION TABLE
//------------------------------------------------------------------//




//------------------------------------------------------------------//
// START OF FETCH AND UPDATE PAGINATION FOR VISITORS TABLE 

// Fetch visitors data by search
document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("search-input");
    const visitorTable = document.getElementById("visitor-table");

    if (searchInput && visitorTable) {
        searchInput.addEventListener("input", (event) => {
            const query = event.target.value;
            const startDate = document.getElementById("startDate")?.value || "";
            const endDate = document.getElementById("endDate")?.value || "";

            fetch(`fetch-visitors.php?search=${encodeURIComponent(query)}&startDate=${encodeURIComponent(startDate)}&endDate=${encodeURIComponent(endDate)}`)
                .then((response) => {
                    if (!response.ok) throw new Error("Network response was not ok");
                    return response.text();
                })
                .then((data) => {
                    visitorTable.innerHTML = data; // Update table content
                    updateVisitorPagination();
                })
                .catch((error) => console.error("Error fetching data:", error));
        });
    } else {
        console.warn("Search input or visitor table element is missing.");
    }
});

// Function to update visitor pagination dynamically
function updateVisitorPagination() {
    const query = document.getElementById("search-input").value;
    const startDate = document.getElementById("startDate")?.value || "";
    const endDate = document.getElementById("endDate")?.value || "";

    fetch(`fetch-visitors.php?search=${encodeURIComponent(query)}&pagination=true&startDate=${encodeURIComponent(startDate)}&endDate=${encodeURIComponent(endDate)}`)
        .then((response) => {
            if (!response.ok) throw new Error("Network response was not ok");
            return response.text();
        })
        .then((data) => {
            document.getElementById("pagination").innerHTML = data;
            const totalRowsElement = document.getElementById("total-rows");
            if (totalRowsElement) {
                const totalRows = parseInt(totalRowsElement.value);
                const totalPages = Math.ceil(totalRows / 7); // Adjust rows per page if needed
                const currentPage = parseInt(document.getElementById("pagination").querySelector(".active a").textContent);
                document.getElementById("page-info").textContent = `Page ${currentPage} of ${totalPages}`;
            }
        })
        .catch((error) => console.error("Error fetching pagination data:", error));
}

// END OF FETCH AND UPDATE PAGINATION FOR VISITORS TABLE
//------------------------------------------------------------------//



//------------------------------------------------------------------//
// START OF FETCH AND UPDATE PAGINATION FOR ACCOUNT TABLE 

// Fetch account data by search
document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("search-input");
    const accountTable = document.getElementById("account-table");

    if (searchInput) {
        searchInput.addEventListener("input", (event) => {
            const query = event.target.value;
            fetch(`fetch-accounts.php?search=${encodeURIComponent(query)}`)
                .then((response) => {
                    if (!response.ok) throw new Error("Network response was not ok");
                    return response.text();
                })
                .then((data) => {
                    accountTable.innerHTML = data;
                    updateAccountPagination();
                })
                .catch((error) => console.error("Error fetching data:", error));
        });
    }
});

// Function to update account pagination dynamically
function updateAccountPagination() {
    const query = document.getElementById("search-input").value;
    fetch(`fetch-accounts.php?search=${encodeURIComponent(query)}&pagination=true`)
        .then((response) => {
            if (!response.ok) throw new Error("Network response was not ok");
            return response.text();
        })
        .then((data) => {
            document.getElementById("pagination").innerHTML = data;
            const totalRows = parseInt(document.getElementById("total-rows").value);
            const totalPages = Math.ceil(totalRows / 8);
            const currentPage = parseInt(document.getElementById("pagination").querySelector(".active a").textContent);
            document.getElementById("page-info").textContent = `Page ${currentPage} of ${totalPages}`;
        })
        .catch((error) => console.error("Error fetching pagination data:", error));
}

// END OF FETCH AND UPDATE PAGINATION FOR VISITORS TABLE
//------------------------------------------------------------------//


// Fetch insite data by search
document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("searchInsite");
    const visitorTable = document.getElementById("insite-table");

    if (searchInput) {
        searchInput.addEventListener("input", (event) => {
            const query = event.target.value;
            fetch(`fetch-monitor-visitor.php?searchInsite=${encodeURIComponent(query)}`)
                .then((response) => {
                    if (!response.ok) throw new Error("Network response was not ok");
                    return response.text();
                })
                .then((data) => {
                    visitorTable.innerHTML = data;
                })
                .catch((error) => console.error("Error fetching data:", error));
        });
    }
});

// Fetch outgoing data by search
document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("searchOutgoing");
    const visitorTable = document.getElementById("outgoing-table");

    if (searchInput) {
        searchInput.addEventListener("input", (event) => {
            const query = event.target.value;
            fetch(`fetch-monitor-visitor.php?searchOutgoing=${encodeURIComponent(query)}`)
                .then((response) => {
                    if (!response.ok) throw new Error("Network response was not ok");
                    return response.text();
                })
                .then((data) => {
                    visitorTable.innerHTML = data;
                })
                .catch((error) => console.error("Error fetching data:", error));
        });
    }
});


// Pop up modal for adding an account
$(document).ready(function () {
    $("#addAccountBtn").click(function (e) {
        e.preventDefault();

        // Load modal 
        $.get("add-account-modal.php", function (data) {
            $("#modalContainer").html(data);
            $("#exampleModal").modal("show");
        });
    });
});

// Pop up modal for custom range filter
$(document).ready(function () {
    $("#customRangeBtn").click(function (e) {
        e.preventDefault();

        // Load modal 
        $.get("custom-range-modal.php", function (data) {
            $("#modalContainer").html(data);
            $("#customRangeModal").modal("show");
        });
    });
});

// Custom range for XLSX export in Visitor Reports section
$(document).ready(function () {
    $("#customRangeExportXLSXBtn").click(function (e) {
        e.preventDefault();

        // Load modal 
        $.get("custom-range-export-XLSX-modal.php", function (data) {
            $("#modalContainer").html(data);
            $("#customRangeExportXLSXModal").modal("show");
        });
    });
});

// Custom range by office for XLSX export in Visitor Reports section
$(document).ready(function () {
    $("#customOfficeExportXLSXBtn").click(function (e) {
        e.preventDefault();

        // Load modal 
        $.get("custom-office-XLSX-modal.php", function (data) {
            $("#modalContainer").html(data);
            $("#customOfficeExportXLSXModal").modal("show");
        });
    });
});


// Custom range by office for PDF export in Visitor Reports section
$(document).ready(function () {
    $("#customOfficeExportPDFBtn").click(function (e) {
        e.preventDefault();

        // Load modal 
        $.get("custom-office-PDF-modal.php", function (data) {
            $("#modalContainer").html(data);
            $("#customOfficeExportPDFModal").modal("show");
        });
    });
});

// Custom range for PDF export in Visitor Reports section
$(document).ready(function () {
    $("#customRangeExportPDFBtn").click(function (e) {
        e.preventDefault();

        // Load modal 
        $.get("custom-range-export-PDF-modal.php", function (data) {
            $("#modalContainer").html(data);
            $("#customRangeExportPDFModal").modal("show");
        });
    });
});

// Alert message dismiss in 3 seconds
setTimeout(function () {
    var alert = document.querySelector('.alert');
    if (alert) {
        var bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    }
}, 3000);



document.addEventListener('DOMContentLoaded', function () {
    const viewDetailsButtons = document.querySelectorAll('.view-details');
    viewDetailsButtons.forEach(button => {
        button.addEventListener('click', function () {
            const name = this.getAttribute('data-name');
            const age = this.getAttribute('data-age');
            const sex = this.getAttribute('data-sex');
            const code = this.getAttribute('data-code');
            const purpose = this.getAttribute('data-purpose');
            
            document.getElementById('modal-name').textContent = name;
            document.getElementById('modal-age').textContent = age;
            document.getElementById('modal-sex').textContent = sex;
            document.getElementById('modal-code').textContent = code;
            document.getElementById('modal-purpose').textContent = purpose;
            document.getElementById('visitor_code').value = code;

            
        // Dynamically generate QR code for the visitor code
        const qrImage = document.getElementById('qr-code');
        qrImage.src = '../../../includes/generate-qr-code.php?visitor_code=' + encodeURIComponent(code);
        });
    });
});


// Assuming you are using jQuery to set the value in the modal
$('.view-details').on('click', function() {
    let visitorCode = $(this).data('code');  
    $('#visitor_code').val(visitorCode); 
});

// Disable print button and remove the div for the code  if code is null or empty and
document.addEventListener("DOMContentLoaded", function () {
    const viewDetailsButtons = document.querySelectorAll(".view-details");
    viewDetailsButtons.forEach((button) => {
      button.addEventListener("click", function () {
        const code = this.getAttribute("data-code");
        const printButton = document.getElementById("print-button");
        const codeDiv = document.getElementById("modal-code").parentElement;
        if (!code) {
            printButton.style.display = "none";
            codeDiv.style.display = "none";
        } else {
          printButton.classList.remove("btn-danger");
          printButton.classList.add("btn-primary");
          printButton.textContent = "Print";
          printButton.disabled = false;
          codeDiv.style.display = "block";
        }
      });
    });
  });


// show loader for once the browser load to prevent flickering
document.body.innerHTML += '<div class="background-overlay"></div><div class="loader"></div><div class="image-holder"></div>';
window.addEventListener('load', async () => {
    await new Promise(resolve => setTimeout(resolve, 400)); 
    document.querySelector('.background-overlay').style.display = 'none';
    document.querySelector('.loader').style.display = 'none';
    document.querySelector('.image-holder').style.display = 'none';
});

// Reload index.php every 60 seconds
setInterval(() => {
    if (window.location.pathname.endsWith("index") || window.location.pathname.endsWith("monitor-visitor")) {
        window.location.reload();
    }
}, 60000);

// Start of chart js

// Function to initialize Chart.js
function initializeChart(chartId, chartType, chartData, chartOptions) {
    const ctx = document.getElementById(chartId).getContext('2d');
    new Chart(ctx, {
        type: chartType,
        data: chartData,
        options: chartOptions
    });
}

// Function to initialize Chart.js with 3D effect
function initialize3DChart(chartId, chartData, chartOptions, chartTitle) {
    const ctx = document.getElementById(chartId).getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: chartData,
        options: {
            ...chartOptions,
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        color: '#4B4B4B'
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(0, 0, 0, 0.7)',
                    titleColor: '#FFFFFF',
                    bodyColor: '#FFFFFF',
                    borderColor: '#4B4B4B',
                    borderWidth: 1
                },
                title: {
                    display: true,
                    text: chartTitle,
                    color: '#4B4B4B',
                    font: {
                        size: 18,
                        weight: 'bold'
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#4B4B4B'
                    }
                },
                y: {
                    grid: {
                        display: false
                    },
                    beginAtZero: true,
                    ticks: {
                        color: '#4B4B4B',
                        callback: function(value) {
                            return Number.isInteger(value) ? value : null;
                        }
                    }
                }
            }
        }
    });
}

// Ensure visitorData and other datasets are defined
document.addEventListener("DOMContentLoaded", function () {
    if (typeof visitorData === "undefined" || typeof userData === "undefined" || typeof cydoData === "undefined" || typeof pdaoData === "undefined") {
        console.error("Chart data is not defined. Please ensure visitorData, userData, cydoData, and pdaoData are loaded before initializing charts.");
        return;
    }

    const visitorChartData = {
        labels: visitorData.labels,
        datasets: [{
            label: 'Visitors',
            data: visitorData.values,
            backgroundColor: 'rgb(64, 156, 108)',
            borderColor: 'rgba(64, 156, 108, 1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }]
    };

    const userChartData = {
        labels: userData.labels,
        datasets: [{
            label: 'Users',
            data: userData.values,
            backgroundColor: 'rgb(0, 51, 255)',
            borderColor: 'rgba(0, 51, 255, 1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }]
    };

    const cydoChartData = {
        labels: cydoData.labels,
        datasets: [{
            label: 'CYDO',
            data: cydoData.values,
            backgroundColor: 'rgb(255, 79, 76)',
            borderColor: 'rgba(255, 79, 76, 1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }]
    };

    const pdaoChartData = {
        labels: pdaoData.labels,
        datasets: [{
            label: 'PDAO',
            data: pdaoData.values,
            backgroundColor: 'rgb(182, 182, 5)',
            borderColor: 'rgb(182, 182, 5)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }]
    };

    const chartOptions = {
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: function(context) {
                        const max = context.chart.scales.y.max;
                        if (max <= 10) return 1;
                        if (max <= 20) return 2;
                        return 5;
                    }
                }
            }
        }
    };

    initialize3DChart('visitorChart', visitorChartData, chartOptions, 'Total Visitors');
    initialize3DChart('userChart', userChartData, chartOptions, 'Total Users');
    initialize3DChart('cydoChart', cydoChartData, chartOptions, 'Incoming CYDO Visitors');
    initialize3DChart('pdaoChart', pdaoChartData, chartOptions, 'Incoming PDAO Visitors');
});

// Dark mode toggle
document.addEventListener("DOMContentLoaded", function () {
    const toggleButton = document.getElementById("dark-mode-toggle");
    const body = document.body;
    const icon = toggleButton.querySelector("i");

    const savedMode = localStorage.getItem("theme");
    if (savedMode === "dark") {
        body.classList.add("dark-mode");
        icon.classList.remove("fa-moon");
        icon.classList.add("fa-sun");
        toggleButton.textContent = " Light Mode";
        toggleButton.prepend(icon);
    }

    toggleButton.addEventListener("click", function () {
        body.classList.toggle("dark-mode");
        const isDarkMode = body.classList.contains("dark-mode");
        localStorage.setItem("theme", isDarkMode ? "dark" : "light");
        if (isDarkMode) {
            icon.classList.remove("fa-moon");
            icon.classList.add("fa-sun");
            toggleButton.textContent = " Light Mode";
        } else {
            icon.classList.remove("fa-sun");
            icon.classList.add("fa-moon");
            toggleButton.textContent = " Dark Mode";
        }
        toggleButton.prepend(icon);
    });
});



