<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Departemen') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div id="departmentGrid"></div>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            const dataSource = new DevExpress.data.CustomStore({
                key: "id",
                load: function() {
                    return $.getJSON("{{ route('departments.data') }}");
                },
                insert: function(values) {
                    return $.ajax({
                        url: "{{ route('departments.store') }}",
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: values
                    });
                },
                update: function(key, values) {
                    return $.ajax({
                        url: "/departments/" + key,
                        method: "PUT",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: values
                    });
                },
                remove: function(key) {
                    return $.ajax({
                        url: "/departments/" + key,
                        method: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    }).fail(function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            DevExpress.ui.notify(xhr.responseJSON.message, "error", 3000);
                        }
                    });
                }
            });

            $("#departmentGrid").dxDataGrid({
                dataSource: dataSource,
                remoteOperations: false,
                showBorders: true,
                showRowLines: true,
                rowAlternationEnabled: true,
                columnAutoWidth: true,
                filterRow: {
                    visible: true
                },
                searchPanel: {
                    visible: true,
                    placeholder: "Cari..."
                },
                headerFilter: {
                    visible: true
                },
                paging: {
                    pageSize: 15
                },
                pager: {
                    visible: true,
                    showPageSizeSelector: true,
                    allowedPageSizes: [10, 15, 25, 50],
                    showInfo: true
                },
                toolbar: {
                    items: [
                        {
                            location: "after",
                            widget: "dxButton",
                            options: {
                                icon: "add",
                                text: "Tambah Data Departemen",
                                onClick: function(e) {
                                    $("#departmentGrid").dxDataGrid("instance").addRow();
                                }
                            }
                        },
                        "searchPanel"
                    ]
                },
                editing: {
                    mode: "popup",
                    allowAdding: false,
                    allowUpdating: true,
                    allowDeleting: true,
                    texts: {
                        addRow: "Tambah Departemen Baru",
                        editRow: "Edit Departemen",
                        deleteRow: "Hapus Departemen",
                        confirmDeleteMessage: "Apakah Anda yakin ingin menghapus departemen ini?"
                    },
                    popup: {
                        title: "Data Departemen",
                        showTitle: true,
                        width: 450,
                        height: 280
                    },
                    form: {
                        items: ["code", "name"]
                    }
                },
                columns: [
                    {
                        dataField: "code",
                        caption: "Kode",
                        width: 100,
                        validationRules: [
                            { type: "required", message: "Kode wajib diisi" },
                            { type: "stringLength", max: 10, message: "Kode maksimal 10 karakter" }
                        ]
                    },
                    {
                        dataField: "name",
                        caption: "Nama Departemen",
                        validationRules: [
                            { type: "required", message: "Nama departemen wajib diisi" }
                        ]
                    },
                    {
                        dataField: "employees_count",
                        caption: "Jumlah Karyawan",
                        allowEditing: false,
                        width: 150,
                        cellTemplate: function(container, options) {
                            const count = options.value || 0;
                            const badge = '<span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">' +
                                          count + ' orang</span>';
                            $(container).html(badge);
                        }
                    },
                    {
                        dataField: "created_at",
                        caption: "Tanggal Dibuat",
                        dataType: "datetime",
                        allowEditing: false,
                        visible: false
                    }
                ],
                export: {
                    enabled: true,
                    fileName: "Data_Departemen"
                },
                onRowInserted: function(e) {
                    DevExpress.ui.notify("Departemen berhasil ditambahkan", "success", 3000);
                },
                onRowUpdated: function(e) {
                    DevExpress.ui.notify("Data departemen berhasil diupdate", "success", 3000);
                },
                onRowRemoved: function(e) {
                    DevExpress.ui.notify("Departemen berhasil dihapus", "success", 3000);
                }
            });
        });
    </script>
</x-app-layout>
