<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
        }

        div {
            text-align: center;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        h1 {
            color: #333333;
            margin-bottom: 20px;
        }

        p {
            font-size: 14px;
            color: #666666;
            line-height: 1.5;
        }

        a {
    color: #ffffff; /* White text color */
    background-color: #007BFF; /* Blue background color */
    text-decoration: none;
    padding: 10px 20px; /* Add padding to make it look like a button */
    border-radius: 4px; /* Add rounded corners */
    transition: background-color 0.3s ease, color 0.3s ease;
}

a:hover {
    background-color: #0056b3; /* Darker blue on hover */
    color: #ffffff; /* White text color on hover */
}


        /* Style for the image */
        img {
            max-width: 50%;
            height: auto;
            margin-bottom: 20px;
        }

        .p-margin{
            margin-bottom: 20px;
        }

    </style>
</head>
<body>
<div>
    <h1>Student Account</h1>
   
    <p class="p-margin">Login Credentials for your account</p>
    <p class="p-margin">email: {{ $email}}</p>
    <p class="p-margin">password: {{ $password }}</p>
    <a href="http://localhost:5173/login">Click here to login page</a>
</div>
</body>
</html>
