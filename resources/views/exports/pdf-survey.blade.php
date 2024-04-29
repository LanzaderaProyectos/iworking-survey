<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title></title>
    <style>
        .col-12 {
            width: 100%;
        }

        .col-6 {
            width: 50%;
            float: left;
        }


        .form-group {
            width: 100%;
            border-bottom: 1px solid #dee2e6 !important;
            padding-bottom: 15px;
            margin-bottom: 15px;
            page-break-before: avoid;
            page-break-after: avoid;
        }

        .table {
            width: 100%;
            color: #212529;
            border-collapse: collapse;
        }

        .table thead th,
        .table thead td {
            font-weight: 500;
            border-bottom-width: 1px;
            padding-top: 1rem;
            padding-bottom: 1rem;
            border: 1px solid #ebedf2;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #ebedf2;
        }

        .table-bordered th,
        .table-bordered td {}
    </style>
</head>

<body tyle="width: 100%;">
    <div class="container-fluid" style="width: 100%;">
        <div>
            <div class="col-12">
                <h1 class="text-center" id="title">Formulario
                </h1>
            </div>
            <div class="col-6">
                <h4>Código : {{ $survey->survey_number ?? ''}} - {{ $survey->name ?? '' }}<h4>
            </div>
            <div class="col-6">
                <h4>Tipo :
                    @if($survey->type == 'pharmaciesSale')
                    Venta Farmacias
                    @elseif($survey->type == 'medicalPrescription')
                    Prescripción Médica
                    @elseif($survey->type == 'training')
                    Formación
                    @endif
                    <h4>
            </div>
        </div>
        <div>
            <p>
            <h4>Fecha expiración: {{$survey->expiration ?? ''}} </h4>
            <p>
        </div>
        <div id="questions">
            @if(!$onlyOrder)
            @php($numberQuestion = 1)
            @foreach ($survey->sections as $index => $section)
            <div class="col-12">
                <h3>Sección: {{ $section->name }}</h3>
                @foreach ($section->surveyQuestionsMain as $key => $surveyQuestionMain)
                <div class="col-12" style="
                    page-break-after:auto;
                    page-break-before:auto;">
                    @include("survey::exports.pdf-survey-types.{$surveyQuestionMain->question->type}",
                    [
                    'surveyQuestion' => $surveyQuestionMain,
                    'numberQuestion' => $key + 1,
                    'value' => '',
                    'is_child' => false])
                </div>
                @endforeach
            </div>
            @php($numberQuestion += $section->questions->count())
            @endforeach
            @endif
            @if($survey->type == "pharmaciesSale")
            @include('survey::exports.pdf-survey-types.pharmaciesSale')
            @endif
        </div>
    </div>
    <script type="text/php">
        if ( isset($pdf) ) {
            $x = 550;
            $y = 815;
            $text = "{PAGE_NUM} de {PAGE_COUNT}";
            $font = $fontMetrics->get_font("helvetica", "bold");
            $size = 8;
            $color = array(0,0,0);
            $word_space = 0.0;  //  default
            $char_space = 0.0;  //  default
            $angle = 0.0;   //  default
            $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
            $x2 = 25;
            $text = date('Y-m-d');
            $pdf->page_text($x2, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
        }
    </script>
</body>


</html>