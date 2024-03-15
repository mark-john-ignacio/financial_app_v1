<?php
if(!isset($_SESSION)){
    session_start();
}
include "../../Connection/connection_string.php";
$company = $_SESSION['companyid'];
$employee = $_SESSION['employeeid'];

$sql = "SELECT pageid FROM users_access WHERE userid = '$employee'";
$query = mysqli_query($con, $sql);

$page = [];
while($row = $query -> fetch_assoc()){
    array_push($page, $row['pageid']);
}
?>

<!DOCTYPE html>
<!--
Author: Keenthemes
Product Name: Metronic - Bootstrap 5 HTML, VueJS, React, Angular & Laravel Admin Dashboard Theme
Purchase: https://1.envato.market/EA4JP
Website: http://www.keenthemes.com
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
License: For each use you must have a valid license purchased only from above link in order to legally use the theme for your project.
-->
<html lang="en">
<!--begin::Head-->
<head><base href="">
    <title>Metronic - the world's #1 selling Bootstrap Admin Theme Ecosystem for HTML, Vue, React, Angular &amp; Laravel by Keenthemes</title>
    <meta name="description" content="The most advanced Bootstrap Admin Theme on Themeforest trusted by 94,000 beginners and professionals. Multi-demo, Dark Mode, RTL support and complete React, Angular, Vue &amp; Laravel versions. Grab your copy now and get life-time updates for free." />
    <meta name="keywords" content="Metronic, bootstrap, bootstrap 5, Angular, VueJs, React, Laravel, admin themes, web design, figma, web development, free templates, free admin themes, bootstrap theme, bootstrap template, bootstrap dashboard, bootstrap dak mode, bootstrap button, bootstrap datepicker, bootstrap timepicker, fullcalendar, datatables, flaticon" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta charset="utf-8" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="Metronic - Bootstrap 5 HTML, VueJS, React, Angular &amp; Laravel Admin Dashboard Theme" />
    <meta property="og:url" content="https://keenthemes.com/metronic" />
    <meta property="og:site_name" content="Keenthemes | Metronic" />
    <link rel="canonical" href="https://preview.keenthemes.com/metronic8" />
    <link rel="shortcut icon" href="assets/media/logos/favicon.ico" />
    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Page Vendor Stylesheets(used by this page)-->
    <link href="assets/plugins/custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet" type="text/css" />
    <!--end::Page Vendor Stylesheets-->
    <!--begin::Global Stylesheets Bundle(used by all pages)-->
    <link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->
    <!--begin::Apexchart JS-->
    <link href="https://cdn.jsdelivr.net/npm/apexcharts@latest/dist/apexcharts.min.css" rel="stylesheet">
    <!--end::Apexchart JS-->
</head>
<!--end::Head-->
<!--begin::Body-->
<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed toolbar-fixed aside-fixed" style="--kt-toolbar-height:55px;--kt-toolbar-height-tablet-and-mobile:55px">
<!--begin::Main-->
<!--begin::Root-->
<div class="d-flex flex-column flex-root">
    <!--begin::Page-->
            <!--begin::Container-->

            <div id="kt_content_container" class="container-xxl">
                <!--begin::Row-->
                <div class="row gy-5 g-xl-8">

                    <div class="row gy-5 g-xl-10">
                        <!--begin::Col-->
                        <div class="col-xl-4 col-12 col-md-4">
                            <!--begin::Mixed Widget 13-->
                            <div class="card card-xl-stretch mb-xl-10" style="background-color: #F7D9E3">
                                <!--begin::Body-->
                                <div class="card-body d-flex flex-column">
                                    <!--begin::Wrapper-->
                                    <div class="d-flex flex-column flex-grow-1">
                                        <!--begin::Title-->
                                        <a href="#" class="text-dark text-hover-primary fw-bolder fs-3">Total Sales</a>
                                        <!--end::Title-->
                                        <!--begin::Chart-->
                                        <div class="total-sales-chart" style="height: 100px"></div>
                                        <!--end::Chart-->
                                    </div>
                                    <!--end::Wrapper-->
                                    <!--begin::Stats-->
                                    <?php
                                    // Query to get the sum of all nnet values
                                    $query_total_nnet = "SELECT SUM(nnet) AS total_nnet FROM sales";
                                    $result_total_nnet = mysqli_query($con, $query_total_nnet);
                                    $row_total_nnet = mysqli_fetch_assoc($result_total_nnet);
                                    $total_nnet = $row_total_nnet["total_nnet"];

                                    // Output the total net sales
                                    $total_sales = '';
                                    if ($total_nnet !== null) {
                                        $total_sales = number_format($total_nnet, 2, '.', ',');
                                    } else {
                                        // Handle the case when $total_nnet is null
                                        $total_sales = '0.00';
                                    }



                                    ?>
                                    <div class="pt-18">
                                        <!--begin::Symbol-->
                                        <span class="text-dark fw-bolder fs-2x lh-0">₱</span>
                                        <!--end::Symbol-->
                                        <!--begin::Number-->
                                        <span class="text-dark fw-bolder fs-3x me-2 lh-0"><?= $total_sales; ?></span>
                                        <!--end::Number-->
                                        <!--begin::Text-->
                                        <?php
                                        // Get the date range for last week (Monday to Saturday)
                                        $start_of_last_week = date("Y-m-d", strtotime("last monday -1 week"));
                                        $end_of_last_week = date("Y-m-d", strtotime("last sunday"));

                                        // Get the date range for the current week (Monday to today)
                                        $start_of_current_week = date("Y-m-d", strtotime("monday this week"));
                                        $current_date = date("Y-m-d");

                                        // Query to get the total nnet sales for last week
                                        $query_last_week = "SELECT SUM(nnet) AS total_nnet_last_week FROM sales WHERE dcutdate >= '$start_of_last_week' AND dcutdate <= '$end_of_last_week'";
                                        $result_last_week = mysqli_query($con, $query_last_week);
                                        $row_last_week = mysqli_fetch_assoc($result_last_week);
                                        $total_nnet_last_week = $row_last_week["total_nnet_last_week"];

                                        // Query to get the total nnet sales for the current week
                                        $query_current_week = "SELECT SUM(nnet) AS total_nnet_current_week FROM sales WHERE dcutdate >= '$start_of_current_week' AND dcutdate <= '$current_date'";
                                        $result_current_week = mysqli_query($con, $query_current_week);
                                        $row_current_week = mysqli_fetch_assoc($result_current_week);
                                        $total_nnet_current_week = $row_current_week["total_nnet_current_week"];

                                        // Calculate the percentage increase or decrease
                                        $percentage_change = 0;
                                        if ($total_nnet_last_week != 0) {
                                            $percentage_change = (($total_nnet_current_week - $total_nnet_last_week) / $total_nnet_last_week) * 100;
                                        }

                                        // Output the percentage change
                                        if ($percentage_change > 0) {
                                            $percentage_change = "+" . round($percentage_change, 2) . "%";
                                        } elseif ($percentage_change < 0) {
                                            $percentage_change = round($percentage_change, 2) . "%";
                                        } else {
                                            $percentage_change = "No change";
                                        }

                                        ?>
                                        <span class="text-dark fw-bolder fs-6 lh-0"><?=$percentage_change;?> this week</span>
                                        <!--end::Text-->
                                    </div>
                                    <!--end::Stats-->
                                </div>
                                <!--end::Body-->
                            </div>
                            <!--end::Mixed Widget 13-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->

                        <div class="col-xl-4 col-12 col-md-4">
                            <!--begin::Mixed Widget 14-->
                            <div class="card card-xxl-stretch mb-xl-10" style="background-color: #CBF0F4">
                                <!--begin::Body-->
                                <div class="card-body d-flex flex-column">
                                    <!--begin::Wrapper-->
                                    <div class="d-flex flex-column flex-grow-1">
                                        <!--begin::Title-->
                                        <?php
                                        // SQL query to get the top-selling item
                                        $sql = "
                        SELECT s_t.citemno, SUM(s_t.nprice) AS total_price
                        FROM sales_t s_t
                        INNER JOIN sales s ON s.compcode = s_t.compcode AND s.ctranno = s_t.ctranno
                        WHERE s.lapproved = 1 AND s.lvoid = 0
                        GROUP BY s_t.citemno
                        ORDER BY total_price DESC
                        LIMIT 1
                        ";

                                        $result = $con->query($sql);

                                        if ($result->num_rows > 0) {
                                            // Output the widget HTML with the dynamic data
                                            while ($row = $result->fetch_assoc()) {
                                                $topSellingItem = $row['citemno'];
                                                $totalSaleValue = $row['total_price'];
                                            }
                                        }

                                        if ($totalSaleValue !== null) {
                                            $totalSaleValue = number_format($totalSaleValue, 2, '.', ',');
                                        } else {
                                            // Handle the case when $total_nnet is null
                                            $totalSaleValue = '0.00';
                                        }
                                        ?>
                                        <a href="#" class="text-dark text-hover-primary fw-bolder fs-3">Top Selling Item</a>
                                        <div class="fs-6 text-dark fw-bolder lh-1"><?= $topSellingItem; ?></div>
                                        <!--end::Title-->
                                        <!--begin::Chart-->
                                        <div class="mixed-widget-14-chart" style="height: 100px"></div>
                                        <!--end::Chart-->
                                    </div>
                                    <!--end::Wrapper-->
                                    <!--begin::Stats-->
                                    <div class="pt-5">
                                        <!--begin::Symbol-->
                                        <span class="text-dark fw-bolder fs-2x lh-0">₱</span>
                                        <!--end::Symbol-->
                                        <!--begin::Number-->
                                        <span class="text-dark fw-bolder fs-3x me-2 lh-0"><?= $totalSaleValue; ?></span>
                                        <!--end::Number-->
                                        <!--begin::Text-->
                                        <span class="text-dark fw-bolder fs-6 lh-0">+ 12% this week</span>
                                        <!--end::Text-->
                                    </div>
                                    <!--end::Stats-->
                                </div>
                            </div>
                            <!--end::Mixed Widget 14-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-xl-4 col-12 col-md-4">
                            <!--begin::Mixed Widget 14-->
                            <div class="card card-xxl-stretch mb-5 mb-xl-10" style="background-color: #CBD4F4">
                                <!--begin::Body-->
                                <div class="card-body d-flex flex-column">
                                    <!--begin::Wrapper-->
                                    <div class="d-flex flex-column mb-7">
                                        <!--begin::Title-->
                                        <a href="#" class="text-dark text-hover-primary fw-bolder fs-3">Summary</a>
                                        <!--end::Title-->
                                    </div>
                                    <!--end::Wrapper-->
                                    <!--begin::Row-->
                                    <div class="row g-0">
                                        <!--begin::Col-->
                                        <div class="col-6">
                                            <div class="d-flex align-items-center mb-9 me-2">
                                                <!--begin::Symbol-->
                                                <div class="symbol symbol-40px me-3">
                                                    <div class="symbol-label bg-white bg-opacity-50">
                                                        <!--begin::Svg Icon | path: icons/duotune/abstract/abs043.svg-->
                                                        <span class="svg-icon svg-icon-1 svg-icon-dark">
																	<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
																		<path opacity="0.3" d="M22 8H8L12 4H19C19.6 4 20.2 4.39999 20.5 4.89999L22 8ZM3.5 19.1C3.8 19.7 4.4 20 5 20H12L16 16H2L3.5 19.1ZM19.1 20.5C19.7 20.2 20 19.6 20 19V12L16 8V22L19.1 20.5ZM4.9 3.5C4.3 3.8 4 4.4 4 5V12L8 16V2L4.9 3.5Z" fill="black" />
																		<path d="M22 8L20 12L16 8H22ZM8 16L4 12L2 16H8ZM16 16L12 20L16 22V16ZM8 8L12 4L8 2V8Z" fill="black" />
																	</svg>
																</span>
                                                        <!--end::Svg Icon-->
                                                    </div>
                                                </div>
                                                <!--end::Symbol-->
                                                <!--begin::Title-->
                                                <div>
                                                    <div class="fs-5 text-dark fw-bolder lh-1">₱50K</div>
                                                    <div class="fs-7 text-gray-600 fw-bold">Sales</div>
                                                </div>
                                                <!--end::Title-->
                                            </div>
                                        </div>
                                        <!--end::Col-->
                                        <!--begin::Col-->
                                        <div class="col-6">
                                            <div class="d-flex align-items-center mb-9 ms-2">
                                                <!--begin::Symbol-->
                                                <div class="symbol symbol-40px me-3">
                                                    <div class="symbol-label bg-white bg-opacity-50">
                                                        <!--begin::Svg Icon | path: icons/duotune/abstract/abs046.svg-->
                                                        <span class="svg-icon svg-icon-1 svg-icon-dark">
																	<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
																		<path d="M8 22C7.4 22 7 21.6 7 21V9C7 8.4 7.4 8 8 8C8.6 8 9 8.4 9 9V21C9 21.6 8.6 22 8 22Z" fill="black" />
																		<path opacity="0.3" d="M4 15C3.4 15 3 14.6 3 14V6C3 5.4 3.4 5 4 5C4.6 5 5 5.4 5 6V14C5 14.6 4.6 15 4 15ZM13 19V3C13 2.4 12.6 2 12 2C11.4 2 11 2.4 11 3V19C11 19.6 11.4 20 12 20C12.6 20 13 19.6 13 19ZM17 16V5C17 4.4 16.6 4 16 4C15.4 4 15 4.4 15 5V16C15 16.6 15.4 17 16 17C16.6 17 17 16.6 17 16ZM21 18V10C21 9.4 20.6 9 20 9C19.4 9 19 9.4 19 10V18C19 18.6 19.4 19 20 19C20.6 19 21 18.6 21 18Z" fill="black" />
																	</svg>
																</span>
                                                        <!--end::Svg Icon-->
                                                    </div>
                                                </div>
                                                <!--end::Symbol-->
                                                <!--begin::Title-->
                                                <div>
                                                    <div class="fs-5 text-dark fw-bolder lh-1">₱4,5K</div>
                                                    <div class="fs-7 text-gray-600 fw-bold">Revenue</div>
                                                </div>
                                                <!--end::Title-->
                                            </div>
                                        </div>
                                        <!--end::Col-->
                                        <!--begin::Col-->
                                        <div class="col-6">
                                            <div class="d-flex align-items-center me-2">
                                                <!--begin::Symbol-->
                                                <div class="symbol symbol-40px me-3">
                                                    <div class="symbol-label bg-white bg-opacity-50">
                                                        <!--begin::Svg Icon | path: icons/duotune/abstract/abs022.svg-->
                                                        <span class="svg-icon svg-icon-1 svg-icon-dark">
																	<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
																		<path opacity="0.3" d="M11.425 7.325C12.925 5.825 15.225 5.825 16.725 7.325C18.225 8.825 18.225 11.125 16.725 12.625C15.225 14.125 12.925 14.125 11.425 12.625C9.92501 11.225 9.92501 8.825 11.425 7.325ZM8.42501 4.325C5.32501 7.425 5.32501 12.525 8.42501 15.625C11.525 18.725 16.625 18.725 19.725 15.625C22.825 12.525 22.825 7.425 19.725 4.325C16.525 1.225 11.525 1.225 8.42501 4.325Z" fill="black" />
																		<path d="M11.325 17.525C10.025 18.025 8.425 17.725 7.325 16.725C5.825 15.225 5.825 12.925 7.325 11.425C8.825 9.92498 11.125 9.92498 12.625 11.425C13.225 12.025 13.625 12.925 13.725 13.725C14.825 13.825 15.925 13.525 16.725 12.625C17.125 12.225 17.425 11.825 17.525 11.325C17.125 10.225 16.525 9.22498 15.625 8.42498C12.525 5.32498 7.425 5.32498 4.325 8.42498C1.225 11.525 1.225 16.625 4.325 19.725C7.425 22.825 12.525 22.825 15.625 19.725C16.325 19.025 16.925 18.225 17.225 17.325C15.425 18.125 13.225 18.225 11.325 17.525Z" fill="black" />
																	</svg>
																</span>
                                                        <!--end::Svg Icon-->
                                                    </div>
                                                </div>
                                                <!--end::Symbol-->
                                                <!--begin::Title-->
                                                <div>
                                                    <div class="fs-5 text-dark fw-bolder lh-1">40</div>
                                                    <div class="fs-7 text-gray-600 fw-bold">Tasks</div>
                                                </div>
                                                <!--end::Title-->
                                            </div>
                                        </div>
                                        <!--end::Col-->
                                        <!--begin::Col-->
                                        <div class="col-6">
                                            <div class="d-flex align-items-center ms-2">
                                                <!--begin::Symbol-->
                                                <div class="symbol symbol-40px me-3">
                                                    <div class="symbol-label bg-white bg-opacity-50">
                                                        <!--begin::Svg Icon | path: icons/duotune/abstract/abs045.svg-->
                                                        <span class="svg-icon svg-icon-1 svg-icon-dark">
																	<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
																		<path d="M2 11.7127L10 14.1127L22 11.7127L14 9.31274L2 11.7127Z" fill="black" />
																		<path opacity="0.3" d="M20.9 7.91274L2 11.7127V6.81275C2 6.11275 2.50001 5.61274 3.10001 5.51274L20.6 2.01274C21.3 1.91274 22 2.41273 22 3.11273V6.61273C22 7.21273 21.5 7.81274 20.9 7.91274ZM22 16.6127V11.7127L3.10001 15.5127C2.50001 15.6127 2 16.2127 2 16.8127V20.3127C2 21.0127 2.69999 21.6128 3.39999 21.4128L20.9 17.9128C21.5 17.8128 22 17.2127 22 16.6127Z" fill="black" />
																	</svg>
																</span>
                                                        <!--end::Svg Icon-->
                                                    </div>
                                                </div>
                                                <!--end::Symbol-->
                                                <!--begin::Title-->
                                                <div>
                                                    <div class="fs-5 text-dark fw-bolder lh-1">₱5.8M</div>
                                                    <div class="fs-7 text-gray-600 fw-bold">Sales</div>
                                                </div>
                                                <!--end::Title-->
                                            </div>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-->
                                </div>
                            </div>
                            <!--end::Mixed Widget 14-->
                        </div>
                        <!--end::Col-->
                    </div>
                    <div class="col-xxl-4">
                        <!--begin::Mixed Widget 12-->
                        <div class="card card-xl-stretch mb-xl-8">
                            <!--begin::Header-->
                            <div class="card-header border-0 bg-primary py-5">
                                <h3 class="card-title fw-bolder text-white">Sales Progress</h3>
                                <div class="card-toolbar">
                                    <!--begin::Menu-->
                                    <button type="button" class="btn btn-sm btn-icon btn-color-white btn-active-white btn-active-color- border-0 me-n3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                        <!--begin::Svg Icon | path: icons/duotune/general/gen024.svg-->
                                        <span class="svg-icon svg-icon-2">
														<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
															<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																<rect x="5" y="5" width="5" height="5" rx="1" fill="#000000"></rect>
																<rect x="14" y="5" width="5" height="5" rx="1" fill="#000000" opacity="0.3"></rect>
																<rect x="5" y="14" width="5" height="5" rx="1" fill="#000000" opacity="0.3"></rect>
																<rect x="14" y="14" width="5" height="5" rx="1" fill="#000000" opacity="0.3"></rect>
															</g>
														</svg>
													</span>
                                        <!--end::Svg Icon-->
                                    </button>
                                    <!--begin::Menu 3-->
                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-bold w-200px py-3" data-kt-menu="true" style="">
                                        <!--begin::Heading-->
                                        <div class="menu-item px-3">
                                            <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">Payments</div>
                                        </div>
                                        <!--end::Heading-->
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-3">
                                            <a href="#" class="menu-link px-3">Create Invoice</a>
                                        </div>
                                        <!--end::Menu item-->
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-3">
                                            <a href="#" class="menu-link flex-stack px-3">Create Payment
                                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="" data-bs-original-title="Specify a target name for future usage and reference" aria-label="Specify a target name for future usage and reference"></i></a>
                                        </div>
                                        <!--end::Menu item-->
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-3">
                                            <a href="#" class="menu-link px-3">Generate Bill</a>
                                        </div>
                                        <!--end::Menu item-->
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-3" data-kt-menu-trigger="hover" data-kt-menu-placement="right-end">
                                            <a href="#" class="menu-link px-3">
                                                <span class="menu-title">Subscription</span>
                                                <span class="menu-arrow"></span>
                                            </a>
                                            <!--begin::Menu sub-->
                                            <div class="menu-sub menu-sub-dropdown w-175px py-4" style="">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3">Plans</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3">Billing</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3">Statements</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu separator-->
                                                <div class="separator my-2"></div>
                                                <!--end::Menu separator-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <div class="menu-content px-3">
                                                        <!--begin::Switch-->
                                                        <label class="form-check form-switch form-check-custom form-check-solid">
                                                            <!--begin::Input-->
                                                            <input class="form-check-input w-30px h-20px" type="checkbox" value="1" checked="checked" name="notifications">
                                                            <!--end::Input-->
                                                            <!--end::Label-->
                                                            <span class="form-check-label text-muted fs-6">Recuring</span>
                                                            <!--end::Label-->
                                                        </label>
                                                        <!--end::Switch-->
                                                    </div>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu sub-->
                                        </div>
                                        <!--end::Menu item-->
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-3 my-1">
                                            <a href="#" class="menu-link px-3">Settings</a>
                                        </div>
                                        <!--end::Menu item-->
                                    </div>
                                    <!--end::Menu 3-->
                                    <!--end::Menu-->
                                </div>
                            </div>
                            <!--end::Header-->
                            <!--begin::Body-->
                            <div class="card-body p-0" style="position: relative;">
                                <!--begin::Chart-->
                                <div class="mixed-widget-12-chart card-rounded-bottom bg-primary" data-kt-color="primary" style="height: 250px; min-height: 250px;"><div id="apexcharts0tzvkel7" class="apexcharts-canvas apexcharts0tzvkel7 apexcharts-theme-light" style="width: 1189px; height: 250px;"><svg id="SvgjsSvg1248" width="1189" height="250" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.dev" class="apexcharts-svg" xmlns:data="ApexChartsNS" transform="translate(0, 0)" style="background: transparent;"><g id="SvgjsG1250" class="apexcharts-inner apexcharts-graphical" transform="translate(20, 0)"><defs id="SvgjsDefs1249"><linearGradient id="SvgjsLinearGradient1253" x1="0" y1="0" x2="0" y2="1"><stop id="SvgjsStop1254" stop-opacity="0.4" stop-color="rgba(216,227,240,0.4)" offset="0"></stop><stop id="SvgjsStop1255" stop-opacity="0.5" stop-color="rgba(190,209,230,0.5)" offset="1"></stop><stop id="SvgjsStop1256" stop-opacity="0.5" stop-color="rgba(190,209,230,0.5)" offset="1"></stop></linearGradient><clipPath id="gridRectMask0tzvkel7"><rect id="SvgjsRect1258" width="1154" height="251" x="-2.5" y="-0.5" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect></clipPath><clipPath id="forecastMask0tzvkel7"></clipPath><clipPath id="nonForecastMask0tzvkel7"></clipPath><clipPath id="gridRectMarkerMask0tzvkel7"><rect id="SvgjsRect1259" width="1153" height="254" x="-2" y="-2" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect></clipPath></defs><rect id="SvgjsRect1257" width="24.62142857142857" height="250" x="573.3571280343192" y="0" rx="0" ry="0" opacity="1" stroke-width="0" stroke-dasharray="3" fill="url(#SvgjsLinearGradient1253)" class="apexcharts-xcrosshairs" y2="250" filter="none" fill-opacity="0.9" x1="573.3571280343192" x2="573.3571280343192"></rect><g id="SvgjsG1295" class="apexcharts-xaxis" transform="translate(0, 0)"><g id="SvgjsG1296" class="apexcharts-xaxis-texts-g" transform="translate(0, -4)"></g></g><g id="SvgjsG1304" class="apexcharts-grid"><g id="SvgjsG1305" class="apexcharts-gridlines-horizontal" style="display: none;"><line id="SvgjsLine1307" x1="0" y1="0" x2="1149" y2="0" stroke="#eff2f5" stroke-dasharray="4" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine1308" x1="0" y1="25" x2="1149" y2="25" stroke="#eff2f5" stroke-dasharray="4" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine1309" x1="0" y1="50" x2="1149" y2="50" stroke="#eff2f5" stroke-dasharray="4" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine1310" x1="0" y1="75" x2="1149" y2="75" stroke="#eff2f5" stroke-dasharray="4" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine1311" x1="0" y1="100" x2="1149" y2="100" stroke="#eff2f5" stroke-dasharray="4" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine1312" x1="0" y1="125" x2="1149" y2="125" stroke="#eff2f5" stroke-dasharray="4" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine1313" x1="0" y1="150" x2="1149" y2="150" stroke="#eff2f5" stroke-dasharray="4" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine1314" x1="0" y1="175" x2="1149" y2="175" stroke="#eff2f5" stroke-dasharray="4" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine1315" x1="0" y1="200" x2="1149" y2="200" stroke="#eff2f5" stroke-dasharray="4" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine1316" x1="0" y1="225" x2="1149" y2="225" stroke="#eff2f5" stroke-dasharray="4" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine1317" x1="0" y1="250" x2="1149" y2="250" stroke="#eff2f5" stroke-dasharray="4" stroke-linecap="butt" class="apexcharts-gridline"></line></g><g id="SvgjsG1306" class="apexcharts-gridlines-vertical" style="display: none;"></g><line id="SvgjsLine1319" x1="0" y1="250" x2="1149" y2="250" stroke="transparent" stroke-dasharray="0" stroke-linecap="butt"></line><line id="SvgjsLine1318" x1="0" y1="1" x2="0" y2="250" stroke="transparent" stroke-dasharray="0" stroke-linecap="butt"></line></g><g id="SvgjsG1260" class="apexcharts-bar-series apexcharts-plot-series"><g id="SvgjsG1261" class="apexcharts-series" rel="1" seriesName="NetxProfit" data:realIndex="0"><path id="SvgjsPath1265" d="M 57.45 250L 57.45 164.5Q 57.45 162.5 59.45 162.5L 79.07142857142857 162.5Q 81.07142857142857 162.5 81.07142857142857 164.5L 81.07142857142857 164.5L 81.07142857142857 250L 81.07142857142857 250z" fill="rgba(255,255,255,0.25)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="1" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectMask0tzvkel7)" pathTo="M 57.45 250L 57.45 164.5Q 57.45 162.5 59.45 162.5L 79.07142857142857 162.5Q 81.07142857142857 162.5 81.07142857142857 164.5L 81.07142857142857 164.5L 81.07142857142857 250L 81.07142857142857 250z" pathFrom="M 57.45 250L 57.45 250L 81.07142857142857 250L 81.07142857142857 250L 81.07142857142857 250L 81.07142857142857 250L 81.07142857142857 250L 57.45 250" cy="162.5" cx="221.09285714285716" j="0" val="35" barHeight="87.5" barWidth="24.62142857142857"></path><path id="SvgjsPath1267" d="M 221.59285714285716 250L 221.59285714285716 89.5Q 221.59285714285716 87.5 223.59285714285716 87.5L 243.21428571428572 87.5Q 245.21428571428572 87.5 245.21428571428572 89.5L 245.21428571428572 89.5L 245.21428571428572 250L 245.21428571428572 250z" fill="rgba(255,255,255,0.25)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="1" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectMask0tzvkel7)" pathTo="M 221.59285714285716 250L 221.59285714285716 89.5Q 221.59285714285716 87.5 223.59285714285716 87.5L 243.21428571428572 87.5Q 245.21428571428572 87.5 245.21428571428572 89.5L 245.21428571428572 89.5L 245.21428571428572 250L 245.21428571428572 250z" pathFrom="M 221.59285714285716 250L 221.59285714285716 250L 245.21428571428572 250L 245.21428571428572 250L 245.21428571428572 250L 245.21428571428572 250L 245.21428571428572 250L 221.59285714285716 250" cy="87.5" cx="385.23571428571427" j="1" val="65" barHeight="162.5" barWidth="24.62142857142857"></path><path id="SvgjsPath1269" d="M 385.73571428571427 250L 385.73571428571427 64.5Q 385.73571428571427 62.5 387.73571428571427 62.5L 407.35714285714283 62.5Q 409.35714285714283 62.5 409.35714285714283 64.5L 409.35714285714283 64.5L 409.35714285714283 250L 409.35714285714283 250z" fill="rgba(255,255,255,0.25)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="1" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectMask0tzvkel7)" pathTo="M 385.73571428571427 250L 385.73571428571427 64.5Q 385.73571428571427 62.5 387.73571428571427 62.5L 407.35714285714283 62.5Q 409.35714285714283 62.5 409.35714285714283 64.5L 409.35714285714283 64.5L 409.35714285714283 250L 409.35714285714283 250z" pathFrom="M 385.73571428571427 250L 385.73571428571427 250L 409.35714285714283 250L 409.35714285714283 250L 409.35714285714283 250L 409.35714285714283 250L 409.35714285714283 250L 385.73571428571427 250" cy="62.5" cx="549.3785714285714" j="2" val="75" barHeight="187.5" barWidth="24.62142857142857"></path><path id="SvgjsPath1271" d="M 549.8785714285714 250L 549.8785714285714 114.5Q 549.8785714285714 112.5 551.8785714285714 112.5L 571.5 112.5Q 573.5 112.5 573.5 114.5L 573.5 114.5L 573.5 250L 573.5 250z" fill="rgba(255,255,255,0.25)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="1" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectMask0tzvkel7)" pathTo="M 549.8785714285714 250L 549.8785714285714 114.5Q 549.8785714285714 112.5 551.8785714285714 112.5L 571.5 112.5Q 573.5 112.5 573.5 114.5L 573.5 114.5L 573.5 250L 573.5 250z" pathFrom="M 549.8785714285714 250L 549.8785714285714 250L 573.5 250L 573.5 250L 573.5 250L 573.5 250L 573.5 250L 549.8785714285714 250" cy="112.5" cx="713.5214285714285" j="3" val="55" barHeight="137.5" barWidth="24.62142857142857"></path><path id="SvgjsPath1273" d="M 714.0214285714285 250L 714.0214285714285 139.5Q 714.0214285714285 137.5 716.0214285714285 137.5L 735.6428571428571 137.5Q 737.6428571428571 137.5 737.6428571428571 139.5L 737.6428571428571 139.5L 737.6428571428571 250L 737.6428571428571 250z" fill="rgba(255,255,255,0.25)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="1" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectMask0tzvkel7)" pathTo="M 714.0214285714285 250L 714.0214285714285 139.5Q 714.0214285714285 137.5 716.0214285714285 137.5L 735.6428571428571 137.5Q 737.6428571428571 137.5 737.6428571428571 139.5L 737.6428571428571 139.5L 737.6428571428571 250L 737.6428571428571 250z" pathFrom="M 714.0214285714285 250L 714.0214285714285 250L 737.6428571428571 250L 737.6428571428571 250L 737.6428571428571 250L 737.6428571428571 250L 737.6428571428571 250L 714.0214285714285 250" cy="137.5" cx="877.6642857142856" j="4" val="45" barHeight="112.5" barWidth="24.62142857142857"></path><path id="SvgjsPath1275" d="M 878.1642857142856 250L 878.1642857142856 102Q 878.1642857142856 100 880.1642857142856 100L 899.7857142857142 100Q 901.7857142857142 100 901.7857142857142 102L 901.7857142857142 102L 901.7857142857142 250L 901.7857142857142 250z" fill="rgba(255,255,255,0.25)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="1" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectMask0tzvkel7)" pathTo="M 878.1642857142856 250L 878.1642857142856 102Q 878.1642857142856 100 880.1642857142856 100L 899.7857142857142 100Q 901.7857142857142 100 901.7857142857142 102L 901.7857142857142 102L 901.7857142857142 250L 901.7857142857142 250z" pathFrom="M 878.1642857142856 250L 878.1642857142856 250L 901.7857142857142 250L 901.7857142857142 250L 901.7857142857142 250L 901.7857142857142 250L 901.7857142857142 250L 878.1642857142856 250" cy="100" cx="1041.8071428571427" j="5" val="60" barHeight="150" barWidth="24.62142857142857"></path><path id="SvgjsPath1277" d="M 1042.3071428571427 250L 1042.3071428571427 114.5Q 1042.3071428571427 112.5 1044.3071428571427 112.5L 1063.9285714285713 112.5Q 1065.9285714285713 112.5 1065.9285714285713 114.5L 1065.9285714285713 114.5L 1065.9285714285713 250L 1065.9285714285713 250z" fill="rgba(255,255,255,0.25)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="1" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectMask0tzvkel7)" pathTo="M 1042.3071428571427 250L 1042.3071428571427 114.5Q 1042.3071428571427 112.5 1044.3071428571427 112.5L 1063.9285714285713 112.5Q 1065.9285714285713 112.5 1065.9285714285713 114.5L 1065.9285714285713 114.5L 1065.9285714285713 250L 1065.9285714285713 250z" pathFrom="M 1042.3071428571427 250L 1042.3071428571427 250L 1065.9285714285713 250L 1065.9285714285713 250L 1065.9285714285713 250L 1065.9285714285713 250L 1065.9285714285713 250L 1042.3071428571427 250" cy="112.5" cx="1205.9499999999998" j="6" val="55" barHeight="137.5" barWidth="24.62142857142857"></path><g id="SvgjsG1263" class="apexcharts-bar-goals-markers" style="pointer-events: none"><g id="SvgjsG1264" className="apexcharts-bar-goals-groups"></g><g id="SvgjsG1266" className="apexcharts-bar-goals-groups"></g><g id="SvgjsG1268" className="apexcharts-bar-goals-groups"></g><g id="SvgjsG1270" className="apexcharts-bar-goals-groups"></g><g id="SvgjsG1272" className="apexcharts-bar-goals-groups"></g><g id="SvgjsG1274" className="apexcharts-bar-goals-groups"></g><g id="SvgjsG1276" className="apexcharts-bar-goals-groups"></g></g></g><g id="SvgjsG1278" class="apexcharts-series" rel="2" seriesName="Revenue" data:realIndex="1"><path id="SvgjsPath1282" d="M 82.07142857142857 250L 82.07142857142857 152Q 82.07142857142857 150 84.07142857142857 150L 103.69285714285714 150Q 105.69285714285714 150 105.69285714285714 152L 105.69285714285714 152L 105.69285714285714 250L 105.69285714285714 250z" fill="rgba(255,255,255,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="1" stroke-dasharray="0" class="apexcharts-bar-area" index="1" clip-path="url(#gridRectMask0tzvkel7)" pathTo="M 82.07142857142857 250L 82.07142857142857 152Q 82.07142857142857 150 84.07142857142857 150L 103.69285714285714 150Q 105.69285714285714 150 105.69285714285714 152L 105.69285714285714 152L 105.69285714285714 250L 105.69285714285714 250z" pathFrom="M 82.07142857142857 250L 82.07142857142857 250L 105.69285714285714 250L 105.69285714285714 250L 105.69285714285714 250L 105.69285714285714 250L 105.69285714285714 250L 82.07142857142857 250" cy="150" cx="245.71428571428572" j="0" val="40" barHeight="100" barWidth="24.62142857142857"></path><path id="SvgjsPath1284" d="M 246.21428571428572 250L 246.21428571428572 77Q 246.21428571428572 75 248.21428571428572 75L 267.8357142857143 75Q 269.8357142857143 75 269.8357142857143 77L 269.8357142857143 77L 269.8357142857143 250L 269.8357142857143 250z" fill="rgba(255,255,255,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="1" stroke-dasharray="0" class="apexcharts-bar-area" index="1" clip-path="url(#gridRectMask0tzvkel7)" pathTo="M 246.21428571428572 250L 246.21428571428572 77Q 246.21428571428572 75 248.21428571428572 75L 267.8357142857143 75Q 269.8357142857143 75 269.8357142857143 77L 269.8357142857143 77L 269.8357142857143 250L 269.8357142857143 250z" pathFrom="M 246.21428571428572 250L 246.21428571428572 250L 269.8357142857143 250L 269.8357142857143 250L 269.8357142857143 250L 269.8357142857143 250L 269.8357142857143 250L 246.21428571428572 250" cy="75" cx="409.85714285714283" j="1" val="70" barHeight="175" barWidth="24.62142857142857"></path><path id="SvgjsPath1286" d="M 410.35714285714283 250L 410.35714285714283 52Q 410.35714285714283 50 412.35714285714283 50L 431.9785714285714 50Q 433.9785714285714 50 433.9785714285714 52L 433.9785714285714 52L 433.9785714285714 250L 433.9785714285714 250z" fill="rgba(255,255,255,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="1" stroke-dasharray="0" class="apexcharts-bar-area" index="1" clip-path="url(#gridRectMask0tzvkel7)" pathTo="M 410.35714285714283 250L 410.35714285714283 52Q 410.35714285714283 50 412.35714285714283 50L 431.9785714285714 50Q 433.9785714285714 50 433.9785714285714 52L 433.9785714285714 52L 433.9785714285714 250L 433.9785714285714 250z" pathFrom="M 410.35714285714283 250L 410.35714285714283 250L 433.9785714285714 250L 433.9785714285714 250L 433.9785714285714 250L 433.9785714285714 250L 433.9785714285714 250L 410.35714285714283 250" cy="50" cx="574" j="2" val="80" barHeight="200" barWidth="24.62142857142857"></path><path id="SvgjsPath1288" d="M 574.5 250L 574.5 102Q 574.5 100 576.5 100L 596.1214285714286 100Q 598.1214285714286 100 598.1214285714286 102L 598.1214285714286 102L 598.1214285714286 250L 598.1214285714286 250z" fill="rgba(255,255,255,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="1" stroke-dasharray="0" class="apexcharts-bar-area" index="1" clip-path="url(#gridRectMask0tzvkel7)" pathTo="M 574.5 250L 574.5 102Q 574.5 100 576.5 100L 596.1214285714286 100Q 598.1214285714286 100 598.1214285714286 102L 598.1214285714286 102L 598.1214285714286 250L 598.1214285714286 250z" pathFrom="M 574.5 250L 574.5 250L 598.1214285714286 250L 598.1214285714286 250L 598.1214285714286 250L 598.1214285714286 250L 598.1214285714286 250L 574.5 250" cy="100" cx="738.1428571428571" j="3" val="60" barHeight="150" barWidth="24.62142857142857"></path><path id="SvgjsPath1290" d="M 738.6428571428571 250L 738.6428571428571 127Q 738.6428571428571 125 740.6428571428571 125L 760.2642857142857 125Q 762.2642857142857 125 762.2642857142857 127L 762.2642857142857 127L 762.2642857142857 250L 762.2642857142857 250z" fill="rgba(255,255,255,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="1" stroke-dasharray="0" class="apexcharts-bar-area" index="1" clip-path="url(#gridRectMask0tzvkel7)" pathTo="M 738.6428571428571 250L 738.6428571428571 127Q 738.6428571428571 125 740.6428571428571 125L 760.2642857142857 125Q 762.2642857142857 125 762.2642857142857 127L 762.2642857142857 127L 762.2642857142857 250L 762.2642857142857 250z" pathFrom="M 738.6428571428571 250L 738.6428571428571 250L 762.2642857142857 250L 762.2642857142857 250L 762.2642857142857 250L 762.2642857142857 250L 762.2642857142857 250L 738.6428571428571 250" cy="125" cx="902.2857142857142" j="4" val="50" barHeight="125" barWidth="24.62142857142857"></path><path id="SvgjsPath1292" d="M 902.7857142857142 250L 902.7857142857142 89.5Q 902.7857142857142 87.5 904.7857142857142 87.5L 924.4071428571428 87.5Q 926.4071428571428 87.5 926.4071428571428 89.5L 926.4071428571428 89.5L 926.4071428571428 250L 926.4071428571428 250z" fill="rgba(255,255,255,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="1" stroke-dasharray="0" class="apexcharts-bar-area" index="1" clip-path="url(#gridRectMask0tzvkel7)" pathTo="M 902.7857142857142 250L 902.7857142857142 89.5Q 902.7857142857142 87.5 904.7857142857142 87.5L 924.4071428571428 87.5Q 926.4071428571428 87.5 926.4071428571428 89.5L 926.4071428571428 89.5L 926.4071428571428 250L 926.4071428571428 250z" pathFrom="M 902.7857142857142 250L 902.7857142857142 250L 926.4071428571428 250L 926.4071428571428 250L 926.4071428571428 250L 926.4071428571428 250L 926.4071428571428 250L 902.7857142857142 250" cy="87.5" cx="1066.4285714285713" j="5" val="65" barHeight="162.5" barWidth="24.62142857142857"></path><path id="SvgjsPath1294" d="M 1066.9285714285713 250L 1066.9285714285713 102Q 1066.9285714285713 100 1068.9285714285713 100L 1088.55 100Q 1090.55 100 1090.55 102L 1090.55 102L 1090.55 250L 1090.55 250z" fill="rgba(255,255,255,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="1" stroke-dasharray="0" class="apexcharts-bar-area" index="1" clip-path="url(#gridRectMask0tzvkel7)" pathTo="M 1066.9285714285713 250L 1066.9285714285713 102Q 1066.9285714285713 100 1068.9285714285713 100L 1088.55 100Q 1090.55 100 1090.55 102L 1090.55 102L 1090.55 250L 1090.55 250z" pathFrom="M 1066.9285714285713 250L 1066.9285714285713 250L 1090.55 250L 1090.55 250L 1090.55 250L 1090.55 250L 1090.55 250L 1066.9285714285713 250" cy="100" cx="1230.5714285714284" j="6" val="60" barHeight="150" barWidth="24.62142857142857"></path><g id="SvgjsG1280" class="apexcharts-bar-goals-markers" style="pointer-events: none"><g id="SvgjsG1281" className="apexcharts-bar-goals-groups"></g><g id="SvgjsG1283" className="apexcharts-bar-goals-groups"></g><g id="SvgjsG1285" className="apexcharts-bar-goals-groups"></g><g id="SvgjsG1287" className="apexcharts-bar-goals-groups"></g><g id="SvgjsG1289" className="apexcharts-bar-goals-groups"></g><g id="SvgjsG1291" className="apexcharts-bar-goals-groups"></g><g id="SvgjsG1293" className="apexcharts-bar-goals-groups"></g></g></g><g id="SvgjsG1262" class="apexcharts-datalabels" data:realIndex="0"></g><g id="SvgjsG1279" class="apexcharts-datalabels" data:realIndex="1"></g></g><line id="SvgjsLine1320" x1="0" y1="0" x2="1149" y2="0" stroke="#b6b6b6" stroke-dasharray="0" stroke-width="1" stroke-linecap="butt" class="apexcharts-ycrosshairs"></line><line id="SvgjsLine1321" x1="0" y1="0" x2="1149" y2="0" stroke-dasharray="0" stroke-width="0" stroke-linecap="butt" class="apexcharts-ycrosshairs-hidden"></line><g id="SvgjsG1322" class="apexcharts-yaxis-annotations"></g><g id="SvgjsG1323" class="apexcharts-xaxis-annotations"></g><g id="SvgjsG1324" class="apexcharts-point-annotations"></g></g><g id="SvgjsG1303" class="apexcharts-yaxis" rel="0" transform="translate(-18, 0)"></g><g id="SvgjsG1251" class="apexcharts-annotations"></g></svg><div class="apexcharts-legend" style="max-height: 125px;"></div><div class="apexcharts-tooltip apexcharts-theme-light" style="left: 605.668px; top: 66px;"><div class="apexcharts-tooltip-title" style="font-family: inherit; font-size: 12px;">May</div><div class="apexcharts-tooltip-series-group apexcharts-active" style="order: 1; display: flex;"><span class="apexcharts-tooltip-marker" style="background-color: rgb(255, 255, 255); display: none;"></span><div class="apexcharts-tooltip-text" style="font-family: inherit; font-size: 12px;"><div class="apexcharts-tooltip-y-group"><span class="apexcharts-tooltip-text-y-label">Revenue: </span><span class="apexcharts-tooltip-text-y-value">₱60 thousands</span></div><div class="apexcharts-tooltip-goals-group"><span class="apexcharts-tooltip-text-goals-label"></span><span class="apexcharts-tooltip-text-goals-value"></span></div><div class="apexcharts-tooltip-z-group"><span class="apexcharts-tooltip-text-z-label"></span><span class="apexcharts-tooltip-text-z-value"></span></div></div></div><div class="apexcharts-tooltip-series-group" style="order: 2; display: none;"><span class="apexcharts-tooltip-marker" style="background-color: rgb(255, 255, 255); display: none;"></span><div class="apexcharts-tooltip-text" style="font-family: inherit; font-size: 12px;"><div class="apexcharts-tooltip-y-group"><span class="apexcharts-tooltip-text-y-label">Revenue: </span><span class="apexcharts-tooltip-text-y-value">₱60 thousands</span></div><div class="apexcharts-tooltip-goals-group"><span class="apexcharts-tooltip-text-goals-label"></span><span class="apexcharts-tooltip-text-goals-value"></span></div><div class="apexcharts-tooltip-z-group"><span class="apexcharts-tooltip-text-z-label"></span><span class="apexcharts-tooltip-text-z-value"></span></div></div></div></div><div class="apexcharts-yaxistooltip apexcharts-yaxistooltip-0 apexcharts-yaxistooltip-left apexcharts-theme-light"><div class="apexcharts-yaxistooltip-text"></div></div></div></div>
                                <!--end::Chart-->
                                <!--begin::Stats-->
                                <div class="card-rounded bg-body mt-n10 position-relative card-px py-15">
                                    <!--begin::Row-->
                                    <div class="row g-0 mb-7">
                                        <!--begin::Col-->
                                        <div class="col mx-5">
                                            <div class="fs-6 text-gray-400">Avarage Sale</div>
                                            <div class="fs-2 fw-bolder text-gray-800">₱650</div>
                                        </div>
                                        <!--end::Col-->
                                        <!--begin::Col-->
                                        <div class="col mx-5">
                                            <div class="fs-6 text-gray-400">Comissions</div>
                                            <div class="fs-2 fw-bolder text-gray-800">₱29,500</div>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-->
                                    <!--begin::Row-->
                                    <div class="row g-0">
                                        <!--begin::Col-->
                                        <div class="col mx-5">
                                            <div class="fs-6 text-gray-400">Revenue</div>
                                            <div class="fs-2 fw-bolder text-gray-800">₱55,000</div>
                                        </div>
                                        <!--end::Col-->
                                        <!--begin::Col-->
                                        <div class="col mx-5">
                                            <div class="fs-6 text-gray-400">Expenses</div>
                                            <div class="fs-2 fw-bolder text-gray-800">₱1,130,600</div>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-->
                                </div>
                                <!--end::Stats-->
                                <div class="resize-triggers"><div class="expand-trigger"><div style="width: 1190px; height: 439px;"></div></div><div class="contract-trigger"></div></div></div>
                            <!--end::Body-->
                        </div>
                        <!--end::Mixed Widget 12-->
                    </div>
                    <!--end::Col-->
                    <!--begin::Col-->
                    <div class="col-xxl-4">

                  <!--begin::row-->
                        <!--begin::Row-->
                        <div class="row g-6 g-xl-9">
                            <!--begin::Col-->
                            <div class="col-lg-6">
                                <!--begin::Summary-->
                                <div class="card card-flush h-lg-100">
                                    <!--begin::Card header-->
                                    <div class="card-header mt-6">
                                        <!--begin::Card title-->
                                        <div class="card-title flex-column">
                                            <h3 class="fw-bolder mb-1">Sales by Channel</h3>
                                            <div class="fs-6 fw-bold text-gray-400">Breakdown of sales</div>
                                        </div>
                                        <!--end::Card title-->
                                        <!--begin::Card toolbar-->
                                        <div class="card-toolbar">
                                            <a href="#" class="btn btn-light btn-sm">View Orders</a>
                                        </div>
                                        <!--end::Card toolbar-->
                                    </div>
                                    <!--end::Card header-->
                                    <!--begin::Card body-->
                                    <div class="card-body p-9 pt-5">
                                        <!--begin::Wrapper-->
                                        <div class="d-flex flex-wrap">
                                            <!--begin::Chart-->
                                            <div class="position-relative d-flex flex-center h-175px w-175px me-15 mb-7">
                                                <div class="position-absolute translate-middle start-50 top-50 d-flex flex-column flex-center">
                                                    <span class="fs-2qx fw-bolder">237</span>
                                                    <span class="fs-6 fw-bold text-gray-400">Total Sales</span>
                                                </div>
                                                <canvas id="project_overview_chart" width="175" height="175" style="display: block; box-sizing: border-box; height: 175px; width: 175px;"></canvas>
                                            </div>
                                            <!--end::Chart-->
                                            <!--begin::Labels-->
                                            <div class="d-flex flex-column justify-content-center flex-row-fluid pe-11 mb-5">
                                                <!--begin::Label-->
                                                <div class="d-flex fs-6 fw-bold align-items-center mb-3">
                                                    <div class="bullet bg-primary me-3"></div>
                                                    <div class="text-gray-400">Online</div>
                                                    <div class="ms-auto fw-bolder text-gray-700">30</div>
                                                </div>
                                                <!--end::Label-->
                                                <!--begin::Label-->
                                                <div class="d-flex fs-6 fw-bold align-items-center mb-3">
                                                    <div class="bullet bg-success me-3"></div>
                                                    <div class="text-gray-400">Physical Store</div>
                                                    <div class="ms-auto fw-bolder text-gray-700">45</div>
                                                </div>
                                                <!--end::Label-->
                                                <!--begin::Label-->
                                                <div class="d-flex fs-6 fw-bold align-items-center mb-3">
                                                    <div class="bullet bg-danger me-3"></div>
                                                    <div class="text-gray-400">Phone Orders</div>
                                                    <div class="ms-auto fw-bolder text-gray-700">0</div>
                                                </div>
                                                <!--end::Label-->
                                                <!--begin::Label-->
                                                <div class="d-flex fs-6 fw-bold align-items-center">
                                                    <div class="bullet bg-gray-300 me-3"></div>
                                                    <div class="text-gray-400">Credit</div>
                                                    <div class="ms-auto fw-bolder text-gray-700">25</div>
                                                </div>
                                                <!--end::Label-->
                                            </div>
                                            <!--end::Labels-->
                                        </div>
                                        <!--end::Wrapper-->

                                    </div>
                                    <!--end::Card body-->
                                </div>
                                <!--end::Summary-->
                            </div>
                        </div>
                    <!--end::row-->


                        <!--begin::Scrolltop-->
                        <div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                            <span class="svg-icon">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
					<rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="black" />
					<path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="black" />
				</svg>
			</span>
                            <!--end::Svg Icon-->
                        </div>
                        <!--end::Scrolltop-->
                        <!--end::Main-->
                        <script>var hostUrl = "assets/";</script>
                        <!--begin::Javascript-->
                        <!--begin::Global Javascript Bundle(used by all pages)-->
                        <script src="assets/plugins/global/plugins.bundle.js"></script>
                        <script src="assets/js/scripts.bundle.js"></script>
                        <!--end::Global Javascript Bundle-->
                        <!--begin::Page Vendors Javascript(used by this page)-->
                        <script src="assets/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
                        <!--end::Page Vendors Javascript-->
                        <!--begin::Page Custom Javascript(used by this page)-->
                        <script src="assets/js/custom/widgets.js"></script>
                        <script src="assets/js/custom/apps/chat/chat.js"></script>
                        <script src="assets/js/custom/modals/create-app.js"></script>
                        <script src="assets/js/custom/modals/upgrade-plan.js"></script>

                        <script src="assets/js/custom/pages/projects/project/project.js"></script>
                        <script src="assets/js/custom/modals/users-search.js"></script>
                        <script src="assets/js/custom/modals/new-target.js"></script>
                        <script src="assets/js/custom/widgets.js"></script>
                        <script src="assets/js/custom/apps/chat/chat.js"></script>
                        <script src="assets/js/custom/modals/create-app.js"></script>
                        <script src="assets/js/custom/modals/upgrade-plan.js"></script>
                        <!--end::Page Custom Javascript-->
                        <!--begin:: Javascript apex1-->
                        <script src="https://cdn.jsdelivr.net/npm/apexcharts@latest"></script>
                        <script src="js/chart.js"></script>
                        <!--end::Javascript apex1-->
                        <!--end::Javascript-->
</body>
<!--end::Body-->
</html>