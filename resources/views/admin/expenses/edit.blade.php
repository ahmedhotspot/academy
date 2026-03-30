@extends('admin.layouts.master')

@section('title', 'تعديل المصروف')

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'تعديل المصروف',
                    'breadcrumbs' => $breadcrumbs,
                ])

                @include('admin.partials.alerts')

                <form action="{{ route('admin.expenses.update', $expense) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-semibold"><i class="ti ti-pencil me-1"></i> تعديل المصروف</h6>
                        </div>
                        <div class="card-body">
                            @include('admin.expenses._form')
                        </div>
                        <div class="card-footer bg-white border-top d-flex gap-2 justify-content-end">
                            <a href="{{ route('admin.expenses.show', $expense) }}" class="btn btn-light">رجوع</a>
                            <button type="submit" class="btn btn-primary">حفظ</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

