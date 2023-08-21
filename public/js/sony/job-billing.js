function billDescription() {
    let amount = 0;
    $("#total").empty();
    $(".sum-value").each(function() {
        if(this.value === "") {
            this.value = 0;
        }
        amount = parseFloat(amount) + parseFloat(this.value);
    });

    let part_total_amount = 0;
    $(".part-vlaue").each(function() {
        if(this.value === "") {
            this.value = 0;
        }
        part_total_amount = parseFloat(part_total_amount) + parseFloat(this.value);
    });


    let subAmount = 0;
    $(".sub-value").each(function() {
        if(this.value === "") {
            this.value = 0;
        }
        subAmount = parseFloat(subAmount) + parseFloat(this.value);
    });

    let vat = $("#vat").val();
    if(vat === ""){
        vat = 0;
    }
    
    let totalAmount = (amount - subAmount);

    if(vat === 0) {
        $("#total").val(totalAmount);
        $("#storeTotalValue").val(totalAmount);
        $("#spare_subtotal").val(part_total_amount);
    }else {
        // let vatAmount = (totalAmount * parseFloat(vat)) / 100;
        let vatAmount = parseFloat(vat);
        let totalVatAmount = totalAmount + vatAmount;
        $("#spare_subtotal").val(part_total_amount);
        $("#total").val(totalVatAmount);
        $("#storeTotalValue").val(totalVatAmount);
    }
}
