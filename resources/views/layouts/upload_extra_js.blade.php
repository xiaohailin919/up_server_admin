<script>
    function fixVal(ele, minVal) {
        if (ele.value < minVal) {
            ele.value = minVal
        }
    }

    $(document).ready(function () {
        //范围输入框离开焦点，检查当前的输入
        $("input[id='tk_max_amount']").bind("blur", function () {
            fixVal(this, 1)
        });

        $("input[id='tk_internal']").bind("blur", function () {
            fixVal(this, 0)
        });

        $("input[id='da_max_amount']").bind("blur", function () {
            fixVal(this, 1)
        });

        $("input[id='da_internal']").bind("blur", function () {
            fixVal(this, 0)
        });

        $("input[id='upload_interval']").bind("blur", function () {
            fixVal(this, 0)
        });
    });
</script>