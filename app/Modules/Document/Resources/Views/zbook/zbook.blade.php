<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Зачетная книжка</title>
    <style>
        html {
            width: 100%;
            padding: 0;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            width: 100%;
            padding: 0;
            margin: 0 auto;
        }

        .title {
            text-align: center;
            font-size: 2em;
            font-weight: bold;
        }

        .page {
            border: 1px solid #888;
        }

        table.bordered {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px
            }
            table.bordered td {
                border: 1px solid #888;
                padding: 2px;
            }

        .note {
            font-size: 0.8em;
            line-height: 0.8em;
        }

        .line {
            border-bottom: 1px solid #000;
            height: 20px;
            display: inline-block;
        }
    </style>
</head>
<body>
    @include('document::zbook.zbook_titul')
    @include('document::zbook.zbook_retake')
    @foreach($certification as $s => $item)
        @include('document::zbook.zbook_certification', ['item' => $item, 'index' => $s, 'reportCardData' => $reportCardData, 'semester' => $semester])
    @endforeach
    @include('document::zbook.zbook_vkr')
</body>
</html>
