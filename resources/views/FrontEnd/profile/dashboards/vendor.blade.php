<div class="row">
    <div class="col-lg-12">




        <div class="row m-3">
            <div class="col-md-3">
                <div class="card gradient-card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-8">
                                <h5 class="card-title"> المتاجر</h5>
                                <p class="card-text">{{ $vendorSpecificData['storeCountForUser'] }}</p>
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
                                <h5 class="card-title"> عروضك</h5>
                                <p class="card-text">{{ $vendorSpecificData['storeDiscountsCount'] }}</p>
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
                                <h5 class="card-title"> مديونيه</h5>
                                <p class="card-text">{{ $vendorSpecificData['debitCreditValue'] }}</p>
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
                            <div class="col-lg-8">
                                <h5 class="card-title"> الزيارات</h5>
                                <p class="card-text">{{ $vendorSpecificData['userDiscountsCountForStores'] }}</p>
                            </div>
                            <div class="col-lg-4"><i class="fa-solid fa-person-circle-question dashcard_icon"></i></div>

                        </div>

                    </div>
                </div>
            </div>



        </div>



        <div class="row m-3">
            <div class="col-md-6">
                <div class="card gradient-card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-8">
                                <h5 class="card-title"> اجمالي المبيعات</h5>
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

            <select id="selectTimePeriod">
                <option value="day">Day</option>
                <option value="week">Week</option>
                <option value="month">Month</option>
                <option value="year">Year</option>
            

        </div>



    </div>
    <div class="col-lg-6">
        <canvas id="discountChart" width="400" height="200"></canvas>

    </div>
</div>