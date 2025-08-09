@if(session()->has('msg'))
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="solid-successToast" class="toast colored-toast {{ session('type') == 'success' ? 'bg-success' : 'bg-danger' }} text-fixed-white" role="alert" aria-live="assertive"
             aria-atomic="true">
            <div class="toast-header {{ session('type') == 'success' ? 'bg-success' : 'bg-danger' }} text-white">
                <strong class="me-auto">{!! session('msg') !!}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @push('script')
        <script>
            /*Top right toast js */
            const topRightToast = document.getElementById('solid-successToast')
            window.addEventListener('load', function () {
                const toast = new bootstrap.Toast(topRightToast)
                toast.show()
            });
        </script>
    @endpush
@endif

@if($errors->any())
    <div class="alert alert-danger mt-3">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{$error}}</li>
            @endforeach
        </ul>
    </div>
@endif
