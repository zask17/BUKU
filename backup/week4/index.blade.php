<label for="name">Name: </label>
<input type="text" id="myIdname">
<span id="subbutton">
    <button type="button" id="myButton" onclick="submitText()">Submit</button>
</span>
<br><br>

<span id="freetxt"></span>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function submitText() {
        var btn = $('#subbutton');
        btn.html('Submitting...');
        var name = $('#myIdname').val();
        $.ajax({
            url: "{{ route('week4.ajax_submit') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                name: name
            },
            success: function (response) {
                btn.html('<button type="button" id="myButton" onclick="submitText()">Submit</button>');
                console.log(response);
                if (response.status === 'success') {
                    $('#freetxt').html(response.data.name);
                    Swal.fire({
                        title: "Success!",
                        text: "Your data has been submitted.",
                        icon: "success"
                    });
                    $('#myIdname').val('');
                } else {
                    Swal.fire(
                        'Error!',
                        'Response status not success.',
                        'error'
                    );
                }
                
            },

            error: function (xhr) {
                btn.html('<button type="button" id="myButton" onclick="submitText()">Submit</button>');
                Swal.fire(
                    'Error!',
                    'There was an error submitting your data.',
                    'error'
                );
            }
        });
    }
</script>