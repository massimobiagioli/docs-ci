<div class="card result-list-wrapper">
    <div class="card-header">
        <h3><?php echo $this->lang->line('results'); ?></h3>
    </div>
    <div class="col-12">
        <table id="search_results_datatable" 
           class="table table-striped table-bordered dt-responsive nowrap"
           data-url="<?php echo $url; ?>"
           data-pagelength="<?php echo $page_length; ?>"
           data-noresultsmsg="<?php echo $this->lang->line('no_results'); ?>"
           cellspacing="0" width="100%">
        <thead>
            <tr>
                <th><?php echo $this->lang->line('dt_col_filename'); ?></th>
                <th><?php echo $this->lang->line('dt_col_created'); ?></th>
                <th></th>
            </tr>
        </thead>
    </table>
    </div>
</div>
