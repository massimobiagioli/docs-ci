<?php if (empty($result)): ?>
    <div class="row error-messages p-2 mt-2">
        <div><?php echo $this->lang->line('no_results'); ?></div>
    </div>
<?php else: ?>

    <div class="card">
        <div class="card-header">
            <h3>RISULTATI:</h3>
        </div>

        <table id="search_results_datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Office</th>
                    <th>Age</th>
                    <th>Start date</th>
                    <th>Salary</th>
                </tr>
            </thead>
            <tbody>

                <?php for($i = 0; $i < 30; $i++): ?>
                <tr>
                    <td>Tiger Nixon</td>
                    <td>System Architect</td>
                    <td>Edinburgh</td>
                    <td>61</td>
                    <td>2011/04/25</td>
                    <td>$320,800</td>
                </tr>
                <?php endfor; ?>

            </tbody>
        </table>
    
    </div>

<?php endif; ?>