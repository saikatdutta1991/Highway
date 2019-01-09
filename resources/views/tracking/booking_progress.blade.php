<div class="card acard">
    <div class="collapse show">
        <div class="card-body">
            <small>STATUS</small>
            <div class="progress">
                
                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">Booked</div>

                @if($booking->isBookingCancelled())
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">Canceled</div>
                @else
                    
                    @if($pickupPoint->isDriverStarted())
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">Coming to pickup</div>

                        @if($pickupPoint->isDriverReached()) 
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">Reached to pickup</div>

                            @if($booking->isBoarded()) 
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">Boarded</div>
                            @endif
                            @if($dropPoint->isDriverReached())
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">Completed</div>
                            @endif

                        @endif

                    @endif
                    
                @endif

                
            </div>
        </div>
    </div>
</div>