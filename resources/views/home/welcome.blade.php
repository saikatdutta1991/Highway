@extends('home.layouts.master')
@section('title', $seo_title)
@section('content')
<header class="bg-gradient" id="home">
    <div class="container mt-5">
        <h1>Welcome to <span class="website_name">{{$website_name}}</span>!</h1>
        <p class="tagline" style="max-width: initial;">Enjoy hassle free rides within your own city or outside your city. <br>The ease of travel is on the finger tips, select the dates book your seats and hail towards your
destination. There is so much more from just travelling to a comfort travel. Byroad presents to you various options to travel. **terms and conditions apply**</p>
    </div>
    <div class="img-holder mt-3"><img src="{{asset('web/home/')}}/images/iphonex.png" alt="phone" class="img-fluid"></div>
</header>
<!-- <div class="client-logos my-5">
    <div class="container text-center">
        <img src="{{asset('web/home/')}}/images/client-logos.png" alt="client logos" class="img-fluid">
    </div>
</div> -->
<div class="section light-bg" id="features">
    <div class="container">
        <div class="section-title">
            <small>HIGHLIGHTS</small>
            <h3>Services you love</h3>
        </div>
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="card features">
                    <div class="card-body">
                        <div class="media">
                            <span class="ti-face-smile gradient-fill ti-3x mr-3"></span>
                            <div class="media-body">
                                <h4 class="card-title">City Ride</h4>
                                <p class="card-text">Quick and easy rides within your city, choose from options like auto to SUVs depending on the number of people travelling and move on the go!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card features">
                    <div class="card-body">
                        <div class="media">
                            <span class="ti-settings gradient-fill ti-3x mr-3"></span>
                            <div class="media-body">
                                <h4 class="card-title">Highway Ride</h4>
                                <p class="card-text">Easy travel like never before, schedule your trips few hours ago for out station ride from the available destination, and once confirmed fascinate your travel world or work commitments with a comfort travel in a cab rather than waiting for other modes of transport.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card features">
                    <div class="card-body">
                        <div class="media">
                            <span class="ti-lock gradient-fill ti-3x mr-3"></span>
                            <div class="media-body">
                                <h4 class="card-title">Simple</h4>
                                <p class="card-text">The app is user friendly and easy to use. Click on the sign up option, complete the KYC and there you land on the booking page with self explanatory options.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card features">
                    <div class="card-body">
                        <div class="media">
                            <span class="ti-lock gradient-fill ti-3x mr-3"></span>
                            <div class="media-body">
                                <h4 class="card-title">Secure</h4>
                                <p class="card-text">All the user data is secured and is as per the privacy policy.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- // end .section -->
<div class="section">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 offset-lg-6">
                <!-- <div class="box-icon"><span class="ti-mobile gradient-fill ti-3x"></span></div> -->
                <h2>Our mission statement</h2>
                <p class="mb-4">Our priorities are our customers. We would want to help customers succeed in there travel goals with the available transport options with us. Customers comfort and there choices matter to us. Hence, we believe in delivering quality services with customers as our primary source of facilitators.</p>
                <a href="javascript:void(0)" class="btn btn-primary">Let India travel!!!</a>
            </div>
        </div>
        <div class="perspective-phone">
            <img src="{{asset('web/home/')}}/images/perspective.png" alt="perspective phone" class="img-fluid">
        </div>
    </div>
</div>
<!-- // end .section -->
<div class="section light-bg">
    <div class="container">
        <div class="section-title">
            <small>FEATURES</small>
            <h3>Do more with our app</h3>
        </div>
        <ul class="nav nav-tabs nav-justified" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#communication">Communication</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#schedule">Scheduling</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#messages">Messages</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#livechat">Live Chat</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="communication">
                <div class="d-flex flex-column flex-lg-row">
                    <img src="{{asset('web/home/')}}/images/graphic.png" alt="graphic" class="img-fluid rounded align-self-start mr-lg-5 mb-5 mb-lg-0">
                    <div>
                        <h2>Communicate with ease</h2>
                        <p class="lead">Uniquely underwhelm premium outsourcing with proactive leadership skills. </p>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer rutrum, urna eu pellentesque pretium, nisi nisi fermentum enim, et sagittis dolor nulla vel sapien. Vestibulum sit amet mattis ante. Ut placerat dui eu nulla
                            congue tincidunt ac a nibh. Mauris accumsan pulvinar lorem placerat volutpat. Praesent quis facilisis elit. Sed condimentum neque quis ex porttitor,
                        </p>
                        <p> malesuada faucibus augue aliquet. Sed elit est, eleifend sed dapibus a, semper a eros. Vestibulum blandit vulputate pharetra. Phasellus lobortis leo a nisl euismod, eu faucibus justo sollicitudin. Mauris consectetur, tortor
                            sed tempor malesuada, sem nunc porta augue, in dictum arcu tortor id turpis. Proin aliquet vulputate aliquam.
                        </p>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="schedule">
                <div class="d-flex flex-column flex-lg-row">
                    <div>
                        <h2>Scheduling when you want</h2>
                        <p class="lead">Uniquely underwhelm premium outsourcing with proactive leadership skills. </p>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer rutrum, urna eu pellentesque pretium, nisi nisi fermentum enim, et sagittis dolor nulla vel sapien. Vestibulum sit amet mattis ante. Ut placerat dui eu nulla
                            congue tincidunt ac a nibh. Mauris accumsan pulvinar lorem placerat volutpat. Praesent quis facilisis elit. Sed condimentum neque quis ex porttitor,
                        </p>
                        <p> malesuada faucibus augue aliquet. Sed elit est, eleifend sed dapibus a, semper a eros. Vestibulum blandit vulputate pharetra. Phasellus lobortis leo a nisl euismod, eu faucibus justo sollicitudin. Mauris consectetur, tortor
                            sed tempor malesuada, sem nunc porta augue, in dictum arcu tortor id turpis. Proin aliquet vulputate aliquam.
                        </p>
                    </div>
                    <img src="{{asset('web/home/')}}/images/graphic.png" alt="graphic" class="img-fluid rounded align-self-start mr-lg-5 mb-5 mb-lg-0">
                </div>
            </div>
            <div class="tab-pane fade" id="messages">
                <div class="d-flex flex-column flex-lg-row">
                    <img src="{{asset('web/home/')}}/images/graphic.png" alt="graphic" class="img-fluid rounded align-self-start mr-lg-5 mb-5 mb-lg-0">
                    <div>
                        <h2>Realtime Messaging service</h2>
                        <p class="lead">Uniquely underwhelm premium outsourcing with proactive leadership skills. </p>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer rutrum, urna eu pellentesque pretium, nisi nisi fermentum enim, et sagittis dolor nulla vel sapien. Vestibulum sit amet mattis ante. Ut placerat dui eu nulla
                            congue tincidunt ac a nibh. Mauris accumsan pulvinar lorem placerat volutpat. Praesent quis facilisis elit. Sed condimentum neque quis ex porttitor,
                        </p>
                        <p> malesuada faucibus augue aliquet. Sed elit est, eleifend sed dapibus a, semper a eros. Vestibulum blandit vulputate pharetra. Phasellus lobortis leo a nisl euismod, eu faucibus justo sollicitudin. Mauris consectetur, tortor
                            sed tempor malesuada, sem nunc porta augue, in dictum arcu tortor id turpis. Proin aliquet vulputate aliquam.
                        </p>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="livechat">
                <div class="d-flex flex-column flex-lg-row">
                    <div>
                        <h2>Live chat when you needed</h2>
                        <p class="lead">Uniquely underwhelm premium outsourcing with proactive leadership skills. </p>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer rutrum, urna eu pellentesque pretium, nisi nisi fermentum enim, et sagittis dolor nulla vel sapien. Vestibulum sit amet mattis ante. Ut placerat dui eu nulla
                            congue tincidunt ac a nibh. Mauris accumsan pulvinar lorem placerat volutpat. Praesent quis facilisis elit. Sed condimentum neque quis ex porttitor,
                        </p>
                        <p> malesuada faucibus augue aliquet. Sed elit est, eleifend sed dapibus a, semper a eros. Vestibulum blandit vulputate pharetra. Phasellus lobortis leo a nisl euismod, eu faucibus justo sollicitudin. Mauris consectetur, tortor
                            sed tempor malesuada, sem nunc porta augue, in dictum arcu tortor id turpis. Proin aliquet vulputate aliquam.
                        </p>
                    </div>
                    <img src="{{asset('web/home/')}}/images/graphic.png" alt="graphic" class="img-fluid rounded align-self-start mr-lg-5 mb-5 mb-lg-0">
                </div>
            </div>
        </div>
    </div>
</div>
<!-- // end .section -->
<div class="section">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <img src="{{asset('web/home/')}}/images/dualphone.png" alt="dual phone" class="img-fluid">
            </div>
            <div class="col-md-6 d-flex align-items-center">
                <div>
                    <div class="box-icon"><span class="ti-rocket gradient-fill ti-3x"></span></div>
                    <h2>Launch your App</h2>
                    <p class="mb-4">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Obcaecati vel exercitationem eveniet vero maxime ratione </p>
                    <a href="#" class="btn btn-primary">Read more</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- // end .section -->
<div class="section light-bg">
    <div class="container">
        <div class="row">
            <div class="col-md-8 d-flex align-items-center">
                <ul class="list-unstyled ui-steps">
                    <li class="media">
                        <div class="circle-icon mr-4">1</div>
                        <div class="media-body">
                            <h5>Create an Account</h5>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer rutrum, urna eu pellentesque pretium obcaecati vel exercitationem </p>
                        </div>
                    </li>
                    <li class="media my-4">
                        <div class="circle-icon mr-4">2</div>
                        <div class="media-body">
                            <h5>Share with friends</h5>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer rutrum, urna eu pellentesque pretium obcaecati vel exercitationem eveniet</p>
                        </div>
                    </li>
                    <li class="media">
                        <div class="circle-icon mr-4">3</div>
                        <div class="media-body">
                            <h5>Enjoy your life</h5>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer rutrum, urna eu pellentesque pretium obcaecati vel exercitationem </p>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="col-md-4">
                <img src="{{asset('web/home/')}}/images/iphonex.png" alt="iphone" class="img-fluid">
            </div>
        </div>
    </div>
</div>
<!-- // end .section -->
<div class="section">
    <div class="container">
        <div class="section-title">
            <small>TESTIMONIALS</small>
            <h3>What our Customers Says</h3>
        </div>
        <div class="testimonials owl-carousel">
            <div class="testimonials-single">
                <img src="{{asset('web/home/')}}/images/client.png" alt="client" class="client-img">
                <blockquote class="blockquote">Uniquely streamline highly efficient scenarios and 24/7 initiatives. Conveniently embrace multifunctional ideas through proactive customer service. Distinctively conceptualize 2.0 intellectual capital via user-centric partnerships.</blockquote>
                <h5 class="mt-4 mb-2">Crystal Gordon</h5>
                <p class="text-primary">United States</p>
            </div>
            <div class="testimonials-single">
                <img src="{{asset('web/home/')}}/images/client.png" alt="client" class="client-img">
                <blockquote class="blockquote">Uniquely streamline highly efficient scenarios and 24/7 initiatives. Conveniently embrace multifunctional ideas through proactive customer service. Distinctively conceptualize 2.0 intellectual capital via user-centric partnerships.</blockquote>
                <h5 class="mt-4 mb-2">Crystal Gordon</h5>
                <p class="text-primary">United States</p>
            </div>
            <div class="testimonials-single">
                <img src="{{asset('web/home/')}}/images/client.png" alt="client" class="client-img">
                <blockquote class="blockquote">Uniquely streamline highly efficient scenarios and 24/7 initiatives. Conveniently embrace multifunctional ideas through proactive customer service. Distinctively conceptualize 2.0 intellectual capital via user-centric partnerships.</blockquote>
                <h5 class="mt-4 mb-2">Crystal Gordon</h5>
                <p class="text-primary">United States</p>
            </div>
        </div>
    </div>
</div>
<!-- // end .section -->
<div class="section light-bg" id="gallery">
    <div class="container">
        <div class="section-title">
            <small>GALLERY</small>
            <h3>App Screenshots</h3>
        </div>
        <div class="img-gallery owl-carousel owl-theme">
            <img src="{{asset('web/home/')}}/images/screen1.jpg" alt="image">
            <img src="{{asset('web/home/')}}/images/screen2.jpg" alt="image">
            <img src="{{asset('web/home/')}}/images/screen3.jpg" alt="image">
            <img src="{{asset('web/home/')}}/images/screen1.jpg" alt="image">
        </div>
    </div>
</div>
<!-- // end .section -->
{{--<div class="section" id="pricing">
    <div class="container">
        <div class="section-title">
            <small>PRICING</small>
            <h3>Upgrade to Pro</h3>
        </div>
        <div class="card-deck">
            <div class="card pricing">
                <div class="card-head">
                    <small class="text-primary">PERSONAL</small>
                    <span class="price">$14<sub>/m</sub></span>
                </div>
                <ul class="list-group list-group-flush">
                    <div class="list-group-item">10 Projects</div>
                    <div class="list-group-item">5 GB Storage</div>
                    <div class="list-group-item">Basic Support</div>
                    <div class="list-group-item"><del>Collaboration</del></div>
                    <div class="list-group-item"><del>Reports and analytics</del></div>
                </ul>
                <div class="card-body">
                    <a href="#" class="btn btn-primary btn-lg btn-block">Choose this Plan</a>
                </div>
            </div>
            <div class="card pricing popular">
                <div class="card-head">
                    <small class="text-primary">FOR TEAMS</small>
                    <span class="price">$29<sub>/m</sub></span>
                </div>
                <ul class="list-group list-group-flush">
                    <div class="list-group-item">Unlimited Projects</div>
                    <div class="list-group-item">100 GB Storage</div>
                    <div class="list-group-item">Priority Support</div>
                    <div class="list-group-item">Collaboration</div>
                    <div class="list-group-item">Reports and analytics</div>
                </ul>
                <div class="card-body">
                    <a href="#" class="btn btn-primary btn-lg btn-block">Choose this Plan</a>
                </div>
            </div>
            <div class="card pricing">
                <div class="card-head">
                    <small class="text-primary">ENTERPRISE</small>
                    <span class="price">$249<sub>/m</sub></span>
                </div>
                <ul class="list-group list-group-flush">
                    <div class="list-group-item">Unlimited Projects</div>
                    <div class="list-group-item">Unlimited Storage</div>
                    <div class="list-group-item">Collaboration</div>
                    <div class="list-group-item">Reports and analytics</div>
                    <div class="list-group-item">Web hooks</div>
                </ul>
                <div class="card-body">
                    <a href="#" class="btn btn-primary btn-lg btn-block">Choose this Plan</a>
                </div>
            </div>
        </div>
        <!-- // end .pricing -->
    </div>
</div>--}}
<!-- // end .section -->
<div class="section pt-0">
    <div class="container">
        <div class="section-title">
            <small>FAQ</small>
            <h3>Frequently Asked Questions</h3>
        </div>
        <div class="row pt-4">
            <div class="col-md-6">
                <h4 class="mb-3">How do I download the app?</h4>
                <p class="light-font mb-5">Click on the Google play icon and you will be directed to download the app. If doesn’t show up automatically, manually type Byroad customer to download customer app.</p>
                <h4 class="mb-3">How do I sign up?</h4>
                <p class="light-font mb-5">You can sign up using your mobile number or Google plus credentials. OTP will be sent on you or mobile and details will be reserved in the app.</p>
            </div>
            <div class="col-md-6">
                <h4 class="mb-3">What payments methods do you accept?</h4>
                <p class="light-font mb-5">We accepts netbanking, card payments and cash payment depending on the city ride or highway ride chosen. Currently we have only online payments for highway ride which is outside the city.</p>
                <h4 class="mb-3">How will I be notified for offers or coupons?</h4>
                <p class="light-font mb-5">Customers may see notifications on the app for offers or coupons available. You might as well receive a text message if notifications are not provided and you can utilize them as per the instructions.</p>
            </div>
        </div>
    </div>
</div>
<!-- // end .section -->
<div class="section bg-gradient">
    <div class="container">
        <div class="call-to-action">
            <div class="box-icon"><span class="ti-mobile gradient-fill ti-3x"></span></div>
            <h2>Download Anywhere</h2>
            <p class="tagline">Available for all major mobile and desktop platforms. Rapidiously visualize optimal ROI rather than enterprise-wide methods of empowerment. </p>
            <div class="my-4">
                <a href="#" class="btn btn-light"><img src="{{asset('web/home/')}}/images/appleicon.png" alt="icon"> App Store</a>
                <a href="#" class="btn btn-light"><img src="{{asset('web/home/')}}/images/playicon.png" alt="icon"> Google play</a>
            </div>
            <p class="text-primary"><small><i>*Works on iOS 10.0.5+, Android Kitkat and above. </i></small></p>
        </div>
    </div>
</div>
@include('home.layouts.address')
@endsection