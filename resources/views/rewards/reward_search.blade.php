
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">


                @include('rewards.table-rows', ['polices' => $polices])


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {

    // AJAX search
    function fetchRewards() {
        let keyword = $("#searchKeyword").val();
        $.ajax({
            url: "{{ route('rewards.index') }}",
            type: "GET",
            data: { keyword },
            success: function (response) {
                $("#rewardTableBody").html(response);
            },
            error: function () {
                $("#rewardTableBody").html('<tr><td colspan="10" class="text-center text-danger">Failed to load data.</td></tr>');
            }
        });
    }

    $("#searchBtn").on("click", function () {
        fetchRewards();
    });

    $("#searchKeyword").on("keyup", function () {
        fetchRewards();
    });
});
</script>

