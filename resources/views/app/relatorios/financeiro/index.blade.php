<x-tenant-app-layout>

<!--begin::Repeater-->
<div id="kt_docs_repeater_basic">
    <!--begin::Form group-->
    <div class="form-group">
        <div data-repeater-list="kt_docs_repeater_basic">
            <div data-repeater-item>
                <div class="form-group row">
                    <div class="col-md-3">
                        <label class="form-label">Name:</label>
                        <input type="email" class="form-control mb-2 mb-md-0" placeholder="Enter full name" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Number:</label>
                        <input type="email" class="form-control mb-2 mb-md-0" placeholder="Enter contact number" />
                    </div>
                    <div class="col-md-2">
                        <div class="form-check form-check-custom form-check-solid mt-2 mt-md-11">
                            <input class="form-check-input" type="checkbox" value="" id="form_checkbox" />
                            <label class="form-check-label" for="form_checkbox">
                                Primary
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <a href="javascript:;" data-repeater-delete class="btn btn-sm btn-light-danger mt-3 mt-md-8">
                            <i class="ki-duotone ki-trash fs-5"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                            Delete
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Form group-->

    <!--begin::Form group-->
    <div class="form-group mt-5">
        <a href="javascript:;" data-repeater-create class="btn btn-light-primary">
            <i class="ki-duotone ki-plus fs-3"></i>
            Add
        </a>
    </div>
    <!--end::Form group-->
</div>
<!--end::Repeater-->

</x-tenant-app-layout>

<script src="/assets/plugins/custom/formrepeater/formrepeater.bundle.js"></script>
