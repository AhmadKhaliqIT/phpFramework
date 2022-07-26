<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/docs/4.0/assets/img/favicons/favicon.ico">

    <title>Starter Template for Bootstrap</title>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.css">
    <script
            src="https://code.jquery.com/jquery-3.6.0.min.js"
            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
            crossorigin="anonymous"></script>

    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.js"></script>

    <!-- Bootstrap core CSS -->
    <link href="https://getbootstrap.com/docs/4.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="https://getbootstrap.com/docs/4.0/examples/starter-template/starter-template.css" rel="stylesheet">
</head>

<body>

<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
    <a class="navbar-brand" href="#">Navbar</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Link</a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" href="#">Disabled</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="http://example.com" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dropdown</a>
                <div class="dropdown-menu" aria-labelledby="dropdown01">
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <a class="dropdown-item" href="#">Something else here</a>
                </div>
            </li>
        </ul>
        <form class="form-inline my-2 my-lg-0">
            <input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
    </div>
</nav>

<main role="main" class="container">

    <div class="starter-template">
        <h1>Bootstrap starter template</h1>
        <p class="lead">Use this document as a way to quickly start any new project.<br> hello {{$name}} {{$family    }}.</p>


        <table id="dataTable" class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>نام و نام خانوادگی</th>
                <th>نام کاربری</th>
                <th>آخرین مراجعه</th>
                <th>مجوزها</th>
                <th>فعال</th>
                <th>عملیات</th>
            </tr>
            </thead>
            <tbody>
            ---
            </tbody>
            <tfoot>
            <tr>
                <th>ID</th>
                <th>نام و نام خانوادگی</th>
                <th>نام کاربری</th>
                <th>آخرین مراجعه</th>
                <th>مجوزها</th>
                <th>فعال</th>
                <th>عملیات</th>
            </tr>
            </tfoot>
        </table>


    </div>

</main><!-- /.container -->

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->

<script src="https://getbootstrap.com/docs/4.0/assets/js/vendor/popper.min.js"></script>
<script src="https://getbootstrap.com/docs/4.0/dist/js/bootstrap.min.js"></script>

<script>


    var oTable = 0;

    $( function () {
        oTable = $( "#dataTable" ).DataTable( {
            processing: true,
            serverSide: true,
            pageLength :-1,
            "lengthMenu": [[1, 2, 100, -1], [1, 2, 100, "همه"]],
            ajax: "http://fw.it/table",
            retrieve: true,
            columns: [ {
                data: 'id',
                name: 'id',
                "width": "5%"
            }, {
                data: 'full_name',
                name: 'full_name',
                orderable: false
            }, {
                data: 'username',
                name: 'username',
                orderable: false,
                "width": "20%"
            }, {
                data: 'last_visit',
                name: 'last_visit',
                "width": "20%"
            }, {
                data: 'employment_date',
                name: 'employment_date',
                "width": "10%",
                searchable: false
            }, {
                data: 'is_locked',
                name: 'is_locked',
                "width": "5%",
                searchable: false
            }, {
                data: 'test',
                name: 'test',
                orderable: false,
                searchable: false,
                "width": "20%"
            } ],
            columnDefs: [
                { width: 50, targets: 0 }
            ],

            fixedColumns: true,
            "order": [[ 0, 'desc' ]],
            "language": {
                "paginate": {
                    "next": "بعدی",
                    "previous": "قبلی"
                },
                "search": "جستجو: ",
                "lengthMenu": "نمایش _MENU_",
                "loadingRecords": "بارگذاری ...",
                "processing": "در حال پردازش ...",
                "zeroRecords": "هیچ رکوردی یافت نشد",
                "info": "نمایش _START_ تا _END_ از _TOTAL_ رکورد",
                "infoEmpty": "نمایش 0 تا 0 از 0 رکورد",
                "infoFiltered": "(فیلترشده از _MAX_ مجموع رکوردها)",
            },
            "autoWidth": false
        } );



    } );


</script>

</body>
</html>


