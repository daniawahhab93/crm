<!-- Include Required Prerequisites -->
<script type="text/javascript" src="//cdn.jsdelivr.net/jquery/1/jquery.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

<!-- Include Date Range Picker -->
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css"/>

<?php
$cur = $this->report_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
if (!empty($range[0])) {
    $start_date = date('F d, Y', strtotime($range[0]));
    $end_date = date('F d, Y', strtotime($range[1]));
}
$status = (isset($status)) ? $status : 'all';
?>

<?php
$chart_year = ($this->session->userdata('chart_year')) ? $this->session->userdata('chart_year') : date('Y');
$cur = $this->report_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
$this->lang->load('calendar', config_item('language'));
?>
<div class="">
    <div class="hidden-print">
        <div class="criteria-band">
            <address class="row" style="margin: 2px;">
                <?php echo form_open(base_url() . 'admin/report/sales_report/' . $filterBy); ?>

                <address class="row">
                    <div class="col-md-3">
                        <label><?= lang('select_warehouse') ?></label>
                        <select class="form-control" name="warehouse_id">
                            <option value="all" <?= ($warehouse_id == 'all') ? 'selected="selected"' : ''; ?>><?= lang('all') ?></option>
                            <?php
                            $all_warehouse = get_result('tbl_warehouse');
                            if (!empty($all_warehouse)) {
                                foreach ($all_warehouse as $v_warehouse) {
                                    ?>
                                    <option value="<?= $v_warehouse->warehouse_id ?>"
                                        <?php
                                        if (!empty($warehouse_id)) {
                                            echo $warehouse_id == $v_warehouse->warehouse_id ? 'selected' : null;
                                        }
                                        ?>
                                    ><?= ucfirst($v_warehouse->warehouse_name) ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </address>
                <address class="row">
                    <div class="col-md-5">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-purple" type="submit" style="padding: 8px 60px;">
                            <?= lang('run_report') ?>
                        </button>
                    </div>
                </address>
            </address>
        </div>
        </form>
    </div>


    <div class="rep-container">
        <div class="page-header text-center">
            <h3 class="reports-headerspacing"><?= lang($filterBy) ?></h3>
            <?php if (!empty($start_date)) { ?>
                <h5><span><?= lang('FROM') ?></span>&nbsp;<?= $start_date ?>
                    &nbsp;<span><?= lang('TO') ?></span>&nbsp;<?= $end_date ?></h5>
            <?php } ?>
        </div>

        <div class="fill-container">
            <div class="row">
                <div class="col-sm-3">
                    <section class="panel panel-info">
                        <div class="panel-body">
                            <div class="clear">
                            <span class="text-dark"><?= lang('total_sales') ?></a>
                                <small class="block text-danger pull-right ">
                                    <?= display_money($this->invoice_model->total_sales($warehouse_id), $cur->symbol) ?>

                                </small>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="col-md-12 b-top">
                    <!-- 1st Quarter -->
                    <div class="col-sm-6 col-xs-12">
                        <div class="widget">
                            <header class="widget-header">
                                <h4 class="widget-title"> <?= lang('1st_quarter') ?>, <?= $chart_year ?></h4>
                            </header><!-- .widget-header -->
                            <hr class="widget-separator">
                            <div class="widget-body p-t-lg">
                                <?php
                                $total_jan = $this->invoice_model->paid_by_date($chart_year, '01',$warehouse_id);
                                $total_feb = $this->invoice_model->paid_by_date($chart_year, '02',$warehouse_id);
                                $total_mar = $this->invoice_model->paid_by_date($chart_year, '03',$warehouse_id);

                                $total_apr = $this->invoice_model->paid_by_date($chart_year, '04',$warehouse_id);
                                $total_may = $this->invoice_model->paid_by_date($chart_year, '05',$warehouse_id);
                                $total_jun = $this->invoice_model->paid_by_date($chart_year, '06',$warehouse_id);
                                $sum = array($total_jan, $total_feb, $total_mar, $total_apr, $total_may, $total_jun);
                                ?>
                                <div class="clearfix mb small text-muted"><?= lang('cal_january') ?>
                                    <div class="pull-right ">
                                        <?= display_money($total_jan, $cur->symbol); ?></div>
                                </div>

                                <div class="clearfix mb small text-muted"><?= lang('cal_february') ?>
                                    <div class="pull-right ">
                                        <?= display_money($total_feb, $cur->symbol); ?>
                                    </div>
                                </div>

                                <div class="clearfix mb small text-muted"><?= lang('cal_march') ?>
                                    <div class="pull-right ">
                                        <?= display_money($total_mar, $cur->symbol); ?>
                                    </div>
                                </div>
                                <div class="clearfix mb small text-muted"><?= lang('cal_april') ?>
                                    <div class="pull-right">
                                        <?= display_money($total_apr, $cur->symbol); ?></div>
                                </div>

                                <div class="clearfix mb small text-muted"><?= lang('cal_may') ?>
                                    <div class="pull-right">
                                        <?= display_money($total_may, $cur->symbol); ?>
                                    </div>
                                </div>

                                <div class="clearfix mb small text-muted"><?= lang('cal_june') ?>
                                    <div class="pull-right">
                                        <?= display_money($total_jun, $cur->symbol); ?>
                                    </div>
                                </div>

                                <div class="clearfix mb small bt pt-sm text-bold text-danger"><?= lang('total') ?>
                                    <div class="pull-right"><strong>
                                            <?= display_money(array_sum($sum), $cur->symbol); ?></strong>
                                    </div>
                                </div>

                            </div><!-- .widget-body -->
                        </div><!-- .widget -->
                    </div>

                    <!-- 3rd Quarter -->

                    <div class="col-sm-6 col-xs-12">
                        <div class="widget">
                            <header class="widget-header">
                                <h4 class="widget-title"> <?= lang('2nd_quarter') ?>, <?= $chart_year ?></h4>
                            </header><!-- .widget-header -->
                            <hr class="widget-separator">
                            <div class="widget-body p-t-lg">
                                <?php
                                $total_jul = $this->invoice_model->paid_by_date($chart_year, '07',$warehouse_id);
                                $total_aug = $this->invoice_model->paid_by_date($chart_year, '08',$warehouse_id);
                                $total_sep = $this->invoice_model->paid_by_date($chart_year, '09',$warehouse_id);
                                $total_oct = $this->invoice_model->paid_by_date($chart_year, '10',$warehouse_id);
                                $total_nov = $this->invoice_model->paid_by_date($chart_year, '11',$warehouse_id);
                                $total_dec = $this->invoice_model->paid_by_date($chart_year, '12',$warehouse_id);
                                $sum = array($total_jul, $total_aug, $total_sep, $total_oct, $total_nov, $total_dec);
                                ?>
                                <div class="clearfix mb small text-muted"><?= lang('cal_july') ?>
                                    <div class="pull-right">
                                        <?= display_money($total_jul, $cur->symbol); ?></div>
                                </div>

                                <div class="clearfix mb small text-muted"><?= lang('cal_august') ?>
                                    <div class="pull-right">
                                        <?= display_money($total_aug, $cur->symbol); ?>
                                    </div>
                                </div>

                                <div class="clearfix mb small text-muted"><?= lang('cal_september') ?>
                                    <div class="pull-right">
                                        <?= display_money($total_sep, $cur->symbol); ?>
                                    </div>
                                </div>
                                <div class="clearfix mb small text-muted"><?= lang('cal_october') ?>
                                    <div class="pull-right">
                                        <?= display_money($total_oct, $cur->symbol); ?></div>
                                </div>

                                <div class="clearfix mb small text-muted"><?= lang('cal_november') ?>
                                    <div class="pull-right">
                                        <?= display_money($total_nov, $cur->symbol); ?>
                                    </div>
                                </div>

                                <div class="clearfix mb small text-muted"><?= lang('cal_december') ?>
                                    <div class="pull-right">
                                        <?= display_money($total_dec, $cur->symbol); ?>
                                    </div>
                                </div>

                                <div class="clearfix mb small bt pt-sm text-bold text-danger"><?= lang('total') ?>
                                    <div class="pull-right"><strong>
                                            <?= display_money(array_sum($sum), $cur->symbol); ?></strong>
                                    </div>
                                </div>

                            </div><!-- .widget-body -->
                        </div><!-- .widget -->
                    </div>
                    <!-- End Quarters -->
                </div>

            </div>

        </div>
        <div class="fill-container">
            <div class="row">
                <div class="col-sm-3">
                    <section class="panel panel-info">
                        <div class="panel-body">
                            <div class="clear">
                            <span class="text-dark"><?= lang('total_return_stock') ?></a>
                                <small class="block text-danger pull-right ">
                                    <?= display_money($this->return_stock_model->total_sales($warehouse_id), $cur->symbol) ?>

                                </small>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="col-md-12 b-top">
                    <!-- 1st Quarter -->
                    <div class="col-sm-6 col-xs-12">
                        <div class="widget">
                            <header class="widget-header">
                                <h4 class="widget-title"> <?= lang('1st_quarter') ?>, <?= $chart_year ?></h4>
                            </header><!-- .widget-header -->
                            <hr class="widget-separator">
                            <div class="widget-body p-t-lg">
                                <?php
                                $total_jan = $this->return_stock_model->paid_by_date($chart_year, '01',$warehouse_id);
                                $total_feb = $this->return_stock_model->paid_by_date($chart_year, '02',$warehouse_id);
                                $total_mar = $this->return_stock_model->paid_by_date($chart_year, '03',$warehouse_id);

                                $total_apr = $this->return_stock_model->paid_by_date($chart_year, '04',$warehouse_id);
                                $total_may = $this->return_stock_model->paid_by_date($chart_year, '05',$warehouse_id);
                                $total_jun = $this->return_stock_model->paid_by_date($chart_year, '06',$warehouse_id);
                                $sum = array($total_jan, $total_feb, $total_mar, $total_apr, $total_may, $total_jun);
                                ?>
                                <div class="clearfix mb small text-muted"><?= lang('cal_january') ?>
                                    <div class="pull-right ">
                                        <?= display_money($total_jan, $cur->symbol); ?></div>
                                </div>

                                <div class="clearfix mb small text-muted"><?= lang('cal_february') ?>
                                    <div class="pull-right ">
                                        <?= display_money($total_feb, $cur->symbol); ?>
                                    </div>
                                </div>

                                <div class="clearfix mb small text-muted"><?= lang('cal_march') ?>
                                    <div class="pull-right ">
                                        <?= display_money($total_mar, $cur->symbol); ?>
                                    </div>
                                </div>
                                <div class="clearfix mb small text-muted"><?= lang('cal_april') ?>
                                    <div class="pull-right">
                                        <?= display_money($total_apr, $cur->symbol); ?></div>
                                </div>

                                <div class="clearfix mb small text-muted"><?= lang('cal_may') ?>
                                    <div class="pull-right">
                                        <?= display_money($total_may, $cur->symbol); ?>
                                    </div>
                                </div>

                                <div class="clearfix mb small text-muted"><?= lang('cal_june') ?>
                                    <div class="pull-right">
                                        <?= display_money($total_jun, $cur->symbol); ?>
                                    </div>
                                </div>

                                <div class="clearfix mb small bt pt-sm text-bold text-danger"><?= lang('total') ?>
                                    <div class="pull-right"><strong>
                                            <?= display_money(array_sum($sum), $cur->symbol); ?></strong>
                                    </div>
                                </div>

                            </div><!-- .widget-body -->
                        </div><!-- .widget -->
                    </div>

                    <!-- 3rd Quarter -->

                    <div class="col-sm-6 col-xs-12">
                        <div class="widget">
                            <header class="widget-header">
                                <h4 class="widget-title"> <?= lang('2nd_quarter') ?>, <?= $chart_year ?></h4>
                            </header><!-- .widget-header -->
                            <hr class="widget-separator">
                            <div class="widget-body p-t-lg">
                                <?php
                                $total_jul = $this->return_stock_model->paid_by_date($chart_year, '07',$warehouse_id);
                                $total_aug = $this->return_stock_model->paid_by_date($chart_year, '08',$warehouse_id);
                                $total_sep = $this->return_stock_model->paid_by_date($chart_year, '09',$warehouse_id);
                                $total_oct = $this->return_stock_model->paid_by_date($chart_year, '10',$warehouse_id);
                                $total_nov = $this->return_stock_model->paid_by_date($chart_year, '11',$warehouse_id);
                                $total_dec = $this->return_stock_model->paid_by_date($chart_year, '12',$warehouse_id);
                                $sum = array($total_jul, $total_aug, $total_sep, $total_oct, $total_nov, $total_dec);
                                ?>
                                <div class="clearfix mb small text-muted"><?= lang('cal_july') ?>
                                    <div class="pull-right">
                                        <?= display_money($total_jul, $cur->symbol); ?></div>
                                </div>

                                <div class="clearfix mb small text-muted"><?= lang('cal_august') ?>
                                    <div class="pull-right">
                                        <?= display_money($total_aug, $cur->symbol); ?>
                                    </div>
                                </div>

                                <div class="clearfix mb small text-muted"><?= lang('cal_september') ?>
                                    <div class="pull-right">
                                        <?= display_money($total_sep, $cur->symbol); ?>
                                    </div>
                                </div>
                                <div class="clearfix mb small text-muted"><?= lang('cal_october') ?>
                                    <div class="pull-right">
                                        <?= display_money($total_oct, $cur->symbol); ?></div>
                                </div>

                                <div class="clearfix mb small text-muted"><?= lang('cal_november') ?>
                                    <div class="pull-right">
                                        <?= display_money($total_nov, $cur->symbol); ?>
                                    </div>
                                </div>

                                <div class="clearfix mb small text-muted"><?= lang('cal_december') ?>
                                    <div class="pull-right">
                                        <?= display_money($total_dec, $cur->symbol); ?>
                                    </div>
                                </div>

                                <div class="clearfix mb small bt pt-sm text-bold text-danger"><?= lang('total') ?>
                                    <div class="pull-right"><strong>
                                            <?= display_money(array_sum($sum), $cur->symbol); ?></strong>
                                    </div>
                                </div>

                            </div><!-- .widget-body -->
                        </div><!-- .widget -->
                    </div>
                    <!-- End Quarters -->
                </div>

            </div>

        </div>


        <script type="text/javascript">

            $('#reportrange').daterangepicker({
                autoUpdateInput: <?= !empty($start_date) ? 'true' : 'false'?>,
                locale: {
                    format: 'MMMM D, YYYY'
                },
                <?php if(!empty($start_date)){?>
                startDate: '<?=$start_date?>',
                endDate: '<?=$end_date?>',
                <?php }?>
                "opens": "right",
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            });
            $('#reportrange').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('MMMM D, YYYY') + ' - ' + picker.endDate.format('MMMM D, YYYY'));
            });

            $('#reportrange').on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
            });
        </script>

