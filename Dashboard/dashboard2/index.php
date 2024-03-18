<?php
global $con;
if(!isset($_SESSION)){
    session_start();
}

include "../../Connection/connection_string.php";
include "analytics/functions.php";
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
<html lang="en">
<!--begin::Head-->
<head><base href="">
    <title>MyxFin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta charset="utf-8" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="MyxFin" />
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
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@latest"></script>
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
                <!--begin::FirstRow-->
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
                                    <div class="pt-18">
                                        <!--begin::Symbol-->
                                        <span class="text-dark fw-bolder fs-2x lh-0">₱</span>
                                        <!--end::Symbol-->
                                        <!--begin::Number-->
                                        <span class="text-dark fw-bolder fs-3x me-2 lh-0"><?= totalSales()['revenue']; ?></span>
                                        <!--end::Number-->
                                        <!--begin::Text-->
                                        <span class="text-dark fw-bolder fs-6 lh-0"><?= totalSales()['percentageChange'];?> this week</span>
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
                                        <a href="#" class="text-dark text-hover-primary fw-bolder fs-3">Top Selling Item</a>
                                        <div class="fs-6 text-dark fw-bolder lh-1"><?= topSellingItem()['name']; ?></div>
                                        <!--end::Title-->
                                        <!--begin::Chart-->
                                        <div class="top-selling-bar-chart" style="height: 100px"></div>
                                        <!--end::Chart-->
                                    </div>
                                    <!--end::Wrapper-->
                                    <!--begin::Stats-->
                                    <div class="pt-10">
                                        <!--begin::Symbol-->
                                        <span class="text-dark fw-bolder fs-2x lh-0">₱</span>
                                        <!--end::Symbol-->
                                        <!--begin::Number-->
                                        <span class="text-dark fw-bolder fs-3x me-2 lh-0"><?= topSellingItem()['revenue']; ?></span>
                                        <!--end::Number-->
                                        <!--begin::Text-->
                                        <span class="text-dark fw-bolder fs-6 lh-0"><?= topSellingItem()['percentageChange']; ?> this week</span>
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
                                <div class="card-body d-flex flex-column ">
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
                                            <div class="d-flex align-items-center mb-15 me-2">
                                                <!--begin::Symbol-->
                                                <div class="symbol symbol-40px me-3">
                                                    <div class="symbol-label bg-white bg-opacity-50">
                                                        <!--begin::Svg Icon -->
                                                        <span class="svg-icon svg-icon-dark svg-icon-2x">
                                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                <defs/>
                                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                    <rect x="0" y="0" width="24" height="24"/>
                                                                    <path d="M2,6 L21,6 C21.5522847,6 22,6.44771525 22,7 L22,17 C22,17.5522847 21.5522847,18 21,18 L2,18 C1.44771525,18 1,17.5522847 1,17 L1,7 C1,6.44771525 1.44771525,6 2,6 Z M11.5,16 C13.709139,16 15.5,14.209139 15.5,12 C15.5,9.790861 13.709139,8 11.5,8 C9.290861,8 7.5,9.790861 7.5,12 C7.5,14.209139 9.290861,16 11.5,16 Z" fill="#000000" opacity="0.3" transform="translate(11.500000, 12.000000) rotate(-345.000000) translate(-11.500000, -12.000000) "/>
                                                                    <path d="M2,6 L21,6 C21.5522847,6 22,6.44771525 22,7 L22,17 C22,17.5522847 21.5522847,18 21,18 L2,18 C1.44771525,18 1,17.5522847 1,17 L1,7 C1,6.44771525 1.44771525,6 2,6 Z M11.5,16 C13.709139,16 15.5,14.209139 15.5,12 C15.5,9.790861 13.709139,8 11.5,8 C9.290861,8 7.5,9.790861 7.5,12 C7.5,14.209139 9.290861,16 11.5,16 Z M11.5,14 C12.6045695,14 13.5,13.1045695 13.5,12 C13.5,10.8954305 12.6045695,10 11.5,10 C10.3954305,10 9.5,10.8954305 9.5,12 C9.5,13.1045695 10.3954305,14 11.5,14 Z" fill="#000000"/>
                                                                </g>
                                                            </svg><!--end::Svg Icon-->
                                                        </span>
                                                        <!--end::Svg Icon-->
                                                    </div>
                                                </div>
                                                <!--end::Symbol-->
                                                <!--begin::Title-->
                                                <div>
                                                    <div class="fs-5 text-dark fw-bolder lh-1">₱<?= totalGrossSales();?></div>
                                                    <div class="fs-7 text-gray-600 fw-bold">Gross Sales</div>
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
                                                    <div class="fs-5 text-dark fw-bolder lh-1">₱<?= totalNetSales(); ?></div>
                                                    <div class="fs-7 text-gray-600 fw-bold">Net Sales</div>
                                                </div>
                                                <!--end::Title-->
                                            </div>
                                        </div>
                                        <!--end::Col-->
                                        <!--begin::Col-->
                                        <div class="col-6">
                                            <div class="d-flex align-items-center me-2 mb-9">
                                                <!--begin::Symbol-->
                                                <div class="symbol symbol-40px me-3">
                                                    <div class="symbol-label bg-white bg-opacity-50">
                                                        <!--begin::Svg Icon | path: icons/duotune/abstract/abs022.svg-->
                                                        <span class="svg-icon svg-icon-1 svg-icon-dark">
                                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                            <defs/>
                                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                <rect x="0" y="0" width="24" height="24"/>
                                                                <rect fill="#000000" opacity="0.3" x="7" y="4" width="10" height="4"/>
                                                                <path d="M7,2 L17,2 C18.1045695,2 19,2.8954305 19,4 L19,20 C19,21.1045695 18.1045695,22 17,22 L7,22 C5.8954305,22 5,21.1045695 5,20 L5,4 C5,2.8954305 5.8954305,2 7,2 Z M8,12 C8.55228475,12 9,11.5522847 9,11 C9,10.4477153 8.55228475,10 8,10 C7.44771525,10 7,10.4477153 7,11 C7,11.5522847 7.44771525,12 8,12 Z M8,16 C8.55228475,16 9,15.5522847 9,15 C9,14.4477153 8.55228475,14 8,14 C7.44771525,14 7,14.4477153 7,15 C7,15.5522847 7.44771525,16 8,16 Z M12,12 C12.5522847,12 13,11.5522847 13,11 C13,10.4477153 12.5522847,10 12,10 C11.4477153,10 11,10.4477153 11,11 C11,11.5522847 11.4477153,12 12,12 Z M12,16 C12.5522847,16 13,15.5522847 13,15 C13,14.4477153 12.5522847,14 12,14 C11.4477153,14 11,14.4477153 11,15 C11,15.5522847 11.4477153,16 12,16 Z M16,12 C16.5522847,12 17,11.5522847 17,11 C17,10.4477153 16.5522847,10 16,10 C15.4477153,10 15,10.4477153 15,11 C15,11.5522847 15.4477153,12 16,12 Z M16,16 C16.5522847,16 17,15.5522847 17,15 C17,14.4477153 16.5522847,14 16,14 C15.4477153,14 15,14.4477153 15,15 C15,15.5522847 15.4477153,16 16,16 Z M16,20 C16.5522847,20 17,19.5522847 17,19 C17,18.4477153 16.5522847,18 16,18 C15.4477153,18 15,18.4477153 15,19 C15,19.5522847 15.4477153,20 16,20 Z M8,18 C7.44771525,18 7,18.4477153 7,19 C7,19.5522847 7.44771525,20 8,20 L12,20 C12.5522847,20 13,19.5522847 13,19 C13,18.4477153 12.5522847,18 12,18 L8,18 Z M7,4 L7,8 L17,8 L17,4 L7,4 Z" fill="#000000"/>
                                                            </g>
                                                            </svg>
																</span>
                                                        <!--end::Svg Icon-->
                                                    </div>
                                                </div>
                                                <!--end::Symbol-->
                                                <!--begin::Title-->
                                                <div>
                                                    <div class="fs-5 text-dark fw-bolder lh-1">₱<?= totalVat()?></div>
                                                    <div class="fs-7 text-gray-600 fw-bold">Total Vat</div>
                                                </div>
                                                <!--end::Title-->
                                            </div>
                                        </div>
                                        <!--end::Col-->
                                        <!--begin::Col-->
                                        <div class="col-6">
                                            <div class="d-flex align-items-center ms-2 mb-9">
                                                <!--begin::Symbol-->
                                                <div class="symbol symbol-40px me-3">
                                                    <div class="symbol-label bg-white bg-opacity-50">
                                                        <!--begin::Svg Icon -->
                                                        <span class="svg-icon svg-icon-dark svg-icon-2x">
                                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <defs/>
                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24" height="24"/>
                                                            <path d="M16.0322024,5.68722152 L5.75790403,15.945742 C5.12139076,16.5812778 5.12059836,17.6124773 5.75613416,18.2489906 C5.75642891,18.2492858 5.75672377,18.2495809 5.75701875,18.2498759 L5.75701875,18.2498759 C6.39304347,18.8859006 7.42424328,18.8859006 8.060268,18.2498759 C8.06056298,18.2495809 8.06085784,18.2492858 8.0611526,18.2489906 L18.3196731,7.9746922 C18.9505124,7.34288268 18.9501191,6.31942463 18.3187946,5.68810005 L18.3187946,5.68810005 C17.68747,5.05677547 16.6640119,5.05638225 16.0322024,5.68722152 Z" fill="#000000" fill-rule="nonzero"/>
                                                            <path d="M9.85714286,6.92857143 C9.85714286,8.54730513 8.5469533,9.85714286 6.93006028,9.85714286 C5.31316726,9.85714286 4,8.54730513 4,6.92857143 C4,5.30983773 5.31316726,4 6.93006028,4 C8.5469533,4 9.85714286,5.30983773 9.85714286,6.92857143 Z M20,17.0714286 C20,18.6901623 18.6898104,20 17.0729174,20 C15.4560244,20 14.1428571,18.6901623 14.1428571,17.0714286 C14.1428571,15.4497247 15.4560244,14.1428571 17.0729174,14.1428571 C18.6898104,14.1428571 20,15.4497247 20,17.0714286 Z" fill="#000000" opacity="0.3"/>
                                                        </g>
                                                            </svg>
                                                        </span>
                                                        <!--end::Svg Icon-->
                                                    </div>
                                                </div>
                                                <!--end::Symbol-->
                                                <!--begin::Title-->
                                                <div>
                                                    <div class="fs-5 text-dark fw-bolder lh-1">₱<?= totalDiscount(); ?></div>
                                                    <div class="fs-7 text-gray-600 fw-bold">Total Discount</div>
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
                <!--end::FirstRow-->
                <!--begin::SecondRow-->
                    <div class="row gy-5 g-xl-10">
                        <!--begin::Col-->
                        <div class="col-xxl-4">
                        <!--begin::Mixed Widget 10-->
                        <div class="card card-xxl-stretch-50 mb-5 mb-xl-8">
                            <!--begin::Body-->
                            <div class="card-body p-0 d-flex justify-content-between flex-column overflow-hidden">
                                <!--begin::Hidden-->
                                <div class="d-flex flex-stack flex-wrap flex-grow-1 px-9 pt-9 pb-3">
                                    <div class="me-2">
                                        <span class="fw-bolder text-gray-800 d-block fs-3">Sales</span>
                                        <span class="text-gray-400 fw-bold"></span>
                                    </div>
                                    <div class="fw-bolder fs-3 text-primary">₱<?=totalSales()["revenue"];?></div>
                                </div>
                                <!--end::Hidden-->
                                <!--begin::Chart-->
                                <div class="sales-progress-bar-chart" data-kt-color="primary" style="height: 175px"></div>
                                <!--end::Chart-->
                            </div>
                        </div>
                        <!--end::Mixed Widget 10-->
                        </div>
                        <!--end::Col-->
                    </div>
                <!--end::SecondRow-->


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

            </div>
            <!--end::Container-->
    <!--end::Page-->
</div>
<!--end::Root-->
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
<script src="js/chart.js"></script>
<!--end::Javascript apex1-->
<!--end::Javascript-->
</body>
<!--end::Body-->
</html>