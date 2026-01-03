<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Karyawan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div id="employeeGrid"></div>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            const dataSource = new DevExpress.data.CustomStore({
                key: "id",
                load: function() {
                    return $.getJSON("{{ route('employees.data') }}");
                },
                insert: function(values) {
                    return $.ajax({
                        url: "{{ route('employees.store') }}",
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: values
                    });
                },
                update: function(key, values) {
                    return $.ajax({
                        url: "/employees/" + key,
                        method: "PUT",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: values
                    });
                },
                remove: function(key) {
                    return $.ajax({
                        url: "/employees/" + key,
                        method: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                }
            });

            $("#employeeGrid").dxDataGrid({
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
                                text: "Tambah Data Karyawan",
                                onClick: function(e) {
                                    $("#employeeGrid").dxDataGrid("instance").addRow();
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
                        addRow: "Tambah Karyawan Baru",
                        editRow: "Edit Karyawan",
                        deleteRow: "Hapus Karyawan",
                        confirmDeleteMessage: "Apakah Anda yakin ingin menghapus karyawan ini?"
                    },
                    popup: {
                        title: "Data Karyawan",
                        showTitle: true,
                        width: 500,
                        height: 350
                    },
                    form: {
                        items: ["name", "department_id", "status"]
                    }
                },
                columns: [
                    {
                        dataField: "employee_id",
                        caption: "ID Karyawan",
                        allowEditing: false,
                        width: 120
                    },
                    {
                        dataField: "name",
                        caption: "Nama",
                        validationRules: [{ type: "required" }]
                    },
                    {
                        dataField: "department_id",
                        caption: "Departemen",
                        lookup: {
                            dataSource: {
                                store: new DevExpress.data.CustomStore({
                                    key: "id",
                                    loadMode: "raw",
                                    load: function() {
                                        return $.getJSON("{{ route('departments.data') }}");
                                    }
                                })
                            },
                            valueExpr: "id",
                            displayExpr: "name"
                        },
                        validationRules: [{ type: "required" }],
                        calculateDisplayValue: function(row) {
                            return row.department ? row.department.name : '';
                        }
                    },
                    {
                        dataField: "status",
                        caption: "Status",
                        lookup: {
                            dataSource: ["Aktif", "Non-aktif"],
                            displayExpr: function(item) {
                                return item;
                            }
                        },
                        validationRules: [{ type: "required" }],
                        cellTemplate: function(container, options) {
                            const badge = options.value === 'Aktif'
                                ? '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">' + options.value + '</span>'
                                : '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">' + options.value + '</span>';
                            $(container).html(badge);
                        }
                    },
                    {
                        dataField: "current_room.room.room_code",
                        caption: "Kamar Saat Ini",
                        allowEditing: false,
                        cellTemplate: function(container, options) {
                            const roomCode = options.value || '-';
                            $(container).text(roomCode);
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
                    fileName: "Data_Karyawan"
                },
                onRowInserted: function(e) {
                    DevExpress.ui.notify("Data karyawan berhasil ditambahkan", "success", 3000);
                },
                onRowUpdated: function(e) {
                    DevExpress.ui.notify("Data karyawan berhasil diupdate", "success", 3000);
                },
                onRowRemoved: function(e) {
                    DevExpress.ui.notify("Data karyawan berhasil dihapus", "success", 3000);
                }
            });
        });
    </script>
</x-app-layout>
