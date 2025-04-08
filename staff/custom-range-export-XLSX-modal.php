<body>
    <div class="modal fade" id="customRangeExportXLSXModal" tabindex="-1" aria-labelledby="customRangeExportXLSXModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customRangeExportXLSXModalLabel">Custom Date Range</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="process/export/AllReport/report-logic.php?type=custom&format=xlsx">
                        <div class="mb-3">
                            <label for="startDate" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="startDateXLSX" name="startDate">
                        </div>
                        <div class="mb-3">
                            <label for="endDate" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="endDateXLSX" name="endDate">
                        </div>
                        <button type="submit" class="btn btn-primary" id="applyDateRange">Download</button>
                    </form>
    
                </div>
            </div>
        </div>
    </div>
</body>