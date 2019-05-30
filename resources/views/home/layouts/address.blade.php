<div class="light-bg py-5" id="contact">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 text-center text-lg-left">
                <p class="mb-2"> <span class="ti-location-pin mr-2"></span> {{$website_address}}</p>
                <div class=" d-block d-sm-inline-block">
                    <p class="mb-2">
                        <span class="ti-email mr-2"></span> <a class="mr-4" href="mailto:{{$website_contact_email}}">{{$website_contact_email}}</a>
                    </p>
                </div>
                <div class="d-block d-sm-inline-block">
                    <p class="mb-0">
                        <span class="ti-headphone-alt mr-2"></span> <a href="tel:{{$website_contact_number}}">{{$website_contact_number}}</a>
                    </p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="social-icons">
                    @if($facebook_social_link)
                    <a href="{{$facebook_social_link}}"><span class="ti-facebook"></span></a>
                    @endif
                    @if($twitter_social_link)
                    <a href="{{$twitter_social_link}}"><span class="ti-twitter-alt"></span></a>
                    @endif
                    @if($instagram_social_link)
                    <a href="{{$instagram_social_link}}"><span class="ti-instagram"></span></a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>