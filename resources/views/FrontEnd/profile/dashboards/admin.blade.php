<div class="row">
    <div class="col-lg-12">

        {{--
/* -------------------------------------------------------------------------- */
/* -------------------------------- First Row ------------------------------- */
/* -------------------------------------------------------------------------- */ --}}

        <div class="row m-3">
            <div class="col-md-3">
                <div class="card gradient-card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-8">
                                <h5 class="card-title">عدد الخصومات</h5>
                                @if($is_admin)
                                <p class="card-text">{{ $userDiscountsCount }}</p> <!-- Display the count for admins -->
                                @else
                                <p class="card-text">Not applicable</p> <!-- Display a message for non-admins -->
                                @endif
                            </div>
                            <div class="col-lg-4"><i class="fa-solid fa-percent dashcard_icon"></i></div>
                        </div>
                    </div>
                </div>
            </div>



            <div class="col-md-3">
                <div class="card gradient-card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-8">
                                <h5 class="card-title"> اجمالي المشتريات</h5>
                                @if($is_admin)
                                <p class="card-text">{{ $totalAfterDiscount }}</p> <!-- Display the total after_discount for admins -->
                                @else
                                <p class="card-text">8000</p> <!-- Display a default value for non-admins -->
                                @endif
                            </div>
                            <div class="col-lg-4"><i class="fa-brands fa-shopify dashcard_icon"></i></div>

                        </div>

                    </div>
                </div>
            </div>


            <div class="col-md-3">
                <div class="card gradient-card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-8">
                                <h5 class="card-title"> الارباح المتبقية </h5>
                                @if($is_admin)
                                <p class="card-text">{{ $totalRemainingProfit }}</p> <!-- Display the total after_discount for admins -->
                                @else
                                <p class="card-text">8000</p> <!-- Display a default value for non-admins -->
                                @endif
                            </div>
                            <div class="col-lg-4"><i class="fa-solid fa-dollar-sign dashcard_icon"></i></div>

                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card gradient-card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <h5 class="card-title"> التجار</h5>
                                <p class="card-text">{{ $vendorCount }}</p>
                            </div>
                            <div class="col-lg-6">
                                <h5 class="card-title"> المستخدمين</h5>
                                <p class="card-text">{{ $nonVendorCount }}</p>
                            </div>
                            {{-- <div class="col-lg-4"><i class="fa-solid fa-user dashcard_icon"></i></div> --}}
                        </div>
                    </div>
                </div>
            </div>



        </div>


        {{--
        /* -------------------------------------------------------------------------- */
        /* ------------------------------- Second Row ------------------------------- */
        /* -------------------------------------------------------------------------- */ --}}

        <div class="row m-3">

            <div class="col-md-3">
                <div class="card gradient-card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-8">
                                <h5 class="card-title"> المتاجر </h5>
                                <p class="card-text">{{ $storeCount }}</p>
                            </div>
                            <div class="col-lg-4"><i class="fa-solid fa-shop dashcard_icon"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card gradient-card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-8">
                                <h5 class="card-title"> العروض السارية</h5>
                                <p class="card-text">{{ $currentDiscountsCount }}</p>
                            </div>
                            <div class="col-lg-4"><i class="fa-solid fa-recycle dashcard_icon"></i></div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-3">
                <div class="card gradient-card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-8">
                                <h5 class="card-title"> الخصومات المرفوضة</h5>
                                <p class="card-text">{{ $rejectedOrdersCount }}</p>
                            </div>
                            <div class="col-lg-4"><i class="fa-solid fa-link-slash dashcard_icon"></i></div>
                        </div>
                    </div>
                </div>
            </div>


        </div>

        {{--
/* -------------------------------------------------------------------------- */
/* ------------------------------- third Row -------------------------------- */
/* -------------------------------------------------------------------------- */ 
--}}
        <div class="row mt-3">
            <div class="row m-3">
                <div class="col-md-6">
                    <div class="card gradient-card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-8">
                                    <h5 class="card-title"> اجمالي التوفير</h5>
                                    <p class="card-text">2512.50 </p>
                                </div>
                                <div class="col-lg-4"><i class="fa-solid fa-wallet dashcard_icon"></i></div>

                            </div>

                        </div>
                    </div>
                </div>



                <div class="col-md-6">
                    <div class="card gradient-card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-8">
                                    <h5 class="card-title"> عدد الخصومات</h5>
                                    <p class="card-text">124</p>
                                </div>
                                <div class="col-lg-4"><i class="fa-solid fa-percent dashcard_icon"></i></div>

                            </div>

                        </div>
                    </div>
                </div>



            </div>



        </div>
        <div class="col-lg-6">

        </div>
    </div>
