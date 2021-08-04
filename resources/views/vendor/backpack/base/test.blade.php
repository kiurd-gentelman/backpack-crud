<div class="m-t-10 m-b-10 p-l-10 p-r-10 p-t-10 p-b-10">
    <div class="row">
        <div class="col-md-12">

{{--            @dump($entry)--}}
            <small>Use the <span class="label label-default">details_row</span> functionality to show more information about the entry, when that information does not fit inside the table column.</small><br><br>
            <strong>Name:</strong> {{ $entry->name }} <br>
            <strong>Description:</strong> {{ $entry->description }} <br>
            <strong>Details:</strong> {!! $entry->details !!} <br>
            <strong>Category:</strong> {{ $entry->category->name }} / {{ $entry->subCategory->name }}  <br>
{{--            <strong>Float:</strong> {{ $entry->float }} <br>--}}
{{--            <strong>Week:</strong> {{ $entry->week }} <br>--}}
{{--            <strong>Month:</strong> {{ $entry->month }} <br>--}}
            etc.
        </div>
    </div>
</div>
<div class="clearfix"></div>

{{--<div>--}}
{{--    <div>--}}
{{--        <img class="img-thumbnail" src="'.asset($image->image_path).'" width="10%">--}}
{{--    </div>--}}

{{--   --}}
{{--</div>--}}

{{--<figure class="figure">--}}
{{--    <img src="'.asset($image->image_path).'" class="figure-img img-fluid rounded" alt="A generic square placeholder image with rounded corners in a figure.">--}}
{{--    <figcaption class="figure-caption"> <a href="'.route('image-delete',$image->id).'" class="btn btn-sm btn-success">test</a></figcaption>--}}
{{--</figure>--}}
<div class="row">
    <div class="col-md-4">
        <div class="thumbnail">
            <a href="/w3images/lights.jpg">
                <img src="/w3images/lights.jpg" alt="Lights" style="width:100%">
                <div class="caption">
                    <p>Lorem ipsum...</p>
                </div>
            </a>
        </div>
    </div>
</div>
