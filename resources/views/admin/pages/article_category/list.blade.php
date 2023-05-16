@extends('admin.layout.master')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>DataTables</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">DataTables</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            @if (session('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h3 class="card-title">DataTable with default features</h3>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="{{ route('article-category.create') }}"
                                            class="btn btn-primary float-right">Create</a>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Is Remove</th>
                                            <th>Option</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($articleCategories as $articleCategory)
                                            <tr>
                                                <td>{{ $articleCategory->name }}</td>
                                                <td>{{ !is_null($articleCategory->deleted_at) ? 'Removed' : 'Available' }}
                                                </td>
                                                <td>
                                                    <form method="post"
                                                        action="{{ route('article-category.destroy', ['article_category' => $articleCategory->id]) }}">
                                                        @csrf
                                                        <a class="btn btn-primary"
                                                            href="{{ route('article-category.edit', ['article_category' => $articleCategory->id]) }}">Detail</a>
                                                        @method('DELETE')
                                                        <button onclick="return confirm('Are you sure ?')" type="submit"
                                                            class="btn btn-danger">Delete</button>
                                                    </form>

                                                    @if ($articleCategory->trashed())
                                                        <form method="post"
                                                            action="{{ route('article-category.restore', ['article_category' => $articleCategory->id]) }}">
                                                            @csrf
                                                            <button onclick="return confirm('Are you sure ?')"
                                                                type="submit" class="btn btn-success">Restore</button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>

                                        @empty
                                            <tr>
                                                <td colspan="1">No Article Category</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
@endsection


@section('js-custom')
    <!-- DataTables -->
    <script src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script>
        $(function() {
            $("#example1").DataTable({
                "responsive": true,
                "autoWidth": false,
            });
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });
    </script>
@endsection
