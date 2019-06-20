@extends('admin.layouts.master')
@section('driver_active', 'active')
@section('title', 'Account Recharge')
@section('driver_accounts_active', 'active')
@section('top-header')
<style>
.balance {
    font-size:24px;
}
.balance-currency-symbol {
    vertical-align:super;
}
#transaction_id_refresh {
    cursor:pointer;
    color:green;    
}
#remarks-char-left {
    position: absolute;
    font-size: 10px;
    right: 0;
}
</style>
@endsection
@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>ACCOUNT RECHARGE & TANSACTIONS</h2>
    </div>
    <div class="row clearfix"  id="recharge_div">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        CREDIT OR DEBIT DRIVER ACCOUNT
                        <small>Here you can credit or debit driver account.</small>
                    </h2>
                </div>
                <div class="body">
                    <form id="recharge-form" method="POST" action="{{route('admin.drivers.accounts.recharge.process')}}">
                        {!! csrf_field() !!}
                        <input type="hidden" value="{{$driver->id}}" name="driver_id">
                        <div class="row clearfix">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <b>Name</b>
                                    <div class="form-line">
                                        <input type="text" class="form-control" value="{{$driver->fullName()}}" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <b>Email</b>
                                    <div class="form-line">
                                        <input type="text" required class="form-control" placeholder="" value="{{$driver->email}}" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <b>Transaction Id</b>
                                <div class="input-group">
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="transaction_id">
                                    </div>
                                    <span class="input-group-addon">
                                        <i class="material-icons" id="transaction_id_refresh" title="Generate transaction id">refresh</i>
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <b>Current balance</b>
                                    <div class="form-control">
                                        <span class="balance-currency-symbol">{{$currency_symbol}}</span>
                                        <span class="balance current-balance" >{{$account->balance}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <b>Action</b><br>
                                <input name="action_type" type="radio" id="credit" class="with-gap radio-col-red" checked value="credit">  
                                <label for="credit">Credit</label>
                                <input name="action_type" type="radio" id="debit" class="with-gap radio-col-red" value="debit">  
                                <label for="debit">Debit</label>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <b>Amount({{$currency_symbol}}) to credit or debit</b>
                                    <div class="form-line">
                                        <input type="number" min="0" required class="form-control" placeholder="" value="0.00" name="amount" step=".01" onblur="this.value=parseFloat(this.value).toFixed(2)">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <b>Closing balance will be</b>
                                    <div class="form-control">
                                        <span class="balance-currency-symbol">{{$currency_symbol}}</span>
                                        <span class="balance closing-balance">{{$account->balance}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-10">
                                <div class="form-group">
                                    <b>Remarks(256 characters). Sms also will be sent</b>
                                    <div class="form-line">
                                        <input type="text" required class="form-control" placeholder="" name="remarks" value="">                                        
                                    </div>
                                    <span id="remarks-char-left">Chars left: 256</span>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <br>
                                <div class="form-group">
                                    <input type="checkbox" id="md_checkbox_21" class="filled-in chk-col-red" name="clear_previous" checked>
                                    <label for="md_checkbox_21">Clear Previous</label>
                                </div>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-12 text-right">
                                <button type="submit" id="" class="btn bg-pink waves-effect">
                                <i class="material-icons">save</i>
                                <span>SAVE</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="row clearfix"  id="transactions_div">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        ACCOUNT TRANSACTIONS
                        <small>This are all account transactions sort by date</small>
                    </h2>
                </div>
                <div class="body table-responsive">
                    <table class="table table-condensed table-hover">
                        <thead>
                            <tr>
                                <th>TRANSACTION ID</th>
                                <th>AMOUNT</th>
                                <th>CLOSING_BALANCE</th>
                                <th>REMARKS</th>
                                <th>DATE</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                            <tr>
                                <td>{{$transaction->trans_id}}</td>
                                <td>{{$transaction->amount}}{{$currency_symbol}}</td>
                                <td>{{$transaction->closing_amount}}{{$currency_symbol}}</td>
                                <td>{{$transaction->remarks}}</td>
                                <td>{{$transaction->createdOn($default_timezone)}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


</div>
</div>
@endsection
@section('bottom')
<script>

    var rechargeApi = "{{route('admin.drivers.accounts.recharge.process')}}";

    /** returns unique transaction id */
    function generateTransactionId() {
        let spart = Math.random().toString(36).substr(2, 10);
        let epart = Math.random().toString(36).substr(2, 5);
        return `${spart}${epart}`.toUpperCase();
    }

    /** create and set new transaction id */
    function setNewTransactionId()
    {   
        let transid = generateTransactionId();
        $("input[name=transaction_id]").val( transid );
    }


    /** calculate and set closing balance */
    function calculateAndSetClosingBalance()
    {
        let currentBalance = parseFloat($(".current-balance").text());
        let amount = parseFloat($("input[name=amount]").val())

        let action = $('input[name=action_type]:checked').val();
        let closingBalance = 0;

        if(action == 'credit') {
            closingBalance = currentBalance + amount;
        } else {
            closingBalance = currentBalance - amount;
        }

        $(".closing-balance").text(closingBalance.toFixed(2));

    }



    $(document).ready(function(){


        $("input[name=remarks]").on('keyup', function(){

            let charleft = 256 - $(this).val().length;
            $('#remarks-char-left').text(charleft);

        });


        $("#recharge-form").on('submit', function(event){
            event.preventDefault();

            if(parseFloat($("input[name=amount]").val()) <= 0) {
                showNotification('bg-red', 'Amount should not be less than zero.', 'bottom', 'center', 'animated flipInX', 'animated flipOutX');
                return;
            }

            let data = $(this).serializeArray();
            console.log(data);

            $.post(rechargeApi, data, function(response){
                console.log(`${rechargeApi} response`, response);
                showNotification('bg-black', response.text, 'top', 'right', 'animated flipInX', 'animated flipOutX');

            }).fail(function(){
                showNotification('bg-red', 'Server error. Contact to admin.', 'top', 'right', 'animated flipInX', 'animated flipOutX');
            });

        });


        
        setNewTransactionId();
        $("#transaction_id_refresh").on('click', setNewTransactionId);
        $("input[name=amount]").on('blur', calculateAndSetClosingBalance).on('focus', function(){
            $(this).select();
        }).on('keyup', calculateAndSetClosingBalance);
        $('input[name=action_type]').on('change', calculateAndSetClosingBalance);

    })
</script>
@endsection