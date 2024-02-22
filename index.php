<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AJAX Login Example</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>

<body>

    <?php echo phpinfo(); ?>

    <script>
        $(document).ready(function() {
            // Replace the URL with your actual login endpoint
            var loginUrl = 'https://picco.animainnovation.com/login/appauth.php';

            // Replace with your actual email and pin
            var requestData = {
                email: 'ja.bobadilla@animainnovation.com',
                pin: 9084
            };

            $.ajax({
                type: 'POST',
                url: loginUrl,
                //contentType: 'application/json',
                data: requestData, // Send data to the server
                dataType: "json",
                success: function(data) {
                    // Handle the successful login response
                    console.log('Login successful', data);
                },
                error: function(xhr, status, error) {
                    // Handle the error response
                    console.error('Login failed', xhr.responseText);
                }
            });
        });
    </script>

</body>

</html>