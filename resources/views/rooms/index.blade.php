<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Kamar Mess & Guest House') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="mb-4">
                    <div id="btnAssignEmployee"></div>
                </div>

                <div id="roomGrid"></div>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            console.log("jQuery ready");
            console.log("DevExtreme available:", typeof DevExpress !== 'undefined');
            console.log("Button element exists:", $("#btnAssignEmployee").length > 0);

            const roomDataSource = new DevExpress.data.CustomStore({
                key: "id",
                load: function() {
                    return $.getJSON("{{ route('rooms.data') }}");
                },
                insert: function(values) {
                    return $.ajax({
                        url: "{{ route('rooms.store') }}",
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: values
                    });
                },
                update: function(key, values) {
                    return $.ajax({
                        url: "/rooms/" + key,
                        method: "PUT",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: values
                    });
                },
                remove: function(key) {
                    return $.ajax({
                        url: "/rooms/" + key,
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

            $("#roomGrid").dxDataGrid({
                dataSource: roomDataSource,
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
                                text: "Tambah Data Kamar",
                                onClick: function(e) {
                                    $("#roomGrid").dxDataGrid("instance").addRow();
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
                        addRow: "Tambah Kamar Baru",
                        editRow: "Edit Kamar",
                        deleteRow: "Hapus Kamar",
                        confirmDeleteMessage: "Apakah Anda yakin ingin menghapus kamar ini?"
                    },
                    popup: {
                        title: "Data Kamar",
                        showTitle: true,
                        width: 450,
                        height: 300
                    },
                    form: {
                        items: ["capacity"]
                    }
                },
                columns: [
                    {
                        dataField: "room_code",
                        caption: "Kode Kamar",
                        width: 120,
                        allowEditing: false
                    },
                    {
                        dataField: "capacity",
                        caption: "Kapasitas",
                        dataType: "number",
                        width: 100,
                        alignment: "center",
                        validationRules: [
                            { type: "required" },
                            { type: "range", min: 1, max: 4, message: "Kapasitas harus antara 1-4" }
                        ]
                    },
                    {
                        dataField: "occupied_count",
                        caption: "Terisi",
                        width: 80,
                        alignment: "center",
                        allowEditing: false,
                        calculateCellValue: function(rowData) {
                            return rowData.occupied_count + "/" + rowData.capacity;
                        },
                        cellTemplate: function(container, options) {
                            const occupied = options.data.occupied_count;
                            const capacity = options.data.capacity;
                            const percentage = capacity > 0 ? (occupied / capacity * 100) : 0;
                            let colorClass = 'text-green-600';
                            if (percentage >= 100) colorClass = 'text-red-600 font-semibold';
                            else if (percentage >= 50) colorClass = 'text-orange-600';
                            $(container).html('<span class="' + colorClass + '">' + occupied + '/' + capacity + '</span>');
                        }
                    },
                    {
                        dataField: "status",
                        caption: "Status",
                        width: 100,
                        alignment: "center",
                        allowEditing: false,
                        cellTemplate: function(container, options) {
                            const badge = options.value === 'Kosong'
                                ? '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Kosong</span>'
                                : options.value === 'Terisi Penuh'
                                ? '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Penuh</span>'
                                : '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">Terisi</span>';
                            $(container).html(badge);
                        }
                    },
                    {
                        caption: "Penghuni Saat Ini",
                        width: 300,
                        allowEditing: false,
                        cellTemplate: function(container, options) {
                            const occupants = options.data.current_occupants || [];
                            console.log("Rendering occupants:", occupants);
                            if (occupants.length > 0) {
                                let html = '<div class="text-sm">';
                                occupants.forEach(function(occ, index) {
                                    console.log("Occupant " + index + ":", occ);
                                    if (occ.is_guest) {
                                        html += '<div class="mb-1">';
                                        html += '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">TAMU</span> ';
                                        html += '<strong>' + (occ.guest_name || 'N/A') + '</strong>';
                                        html += '<div class="text-xs text-gray-500 ml-1">Keperluan: ' + (occ.guest_purpose || 'N/A') + '</div>';
                                        if (occ.guest_duration_days) {
                                            html += '<div class="text-xs text-gray-500 ml-1">Durasi: ' + occ.guest_duration_days + ' hari';
                                            if (occ.estimated_checkout_date) {
                                                const checkoutDate = new Date(occ.estimated_checkout_date);
                                                html += ' (hingga ' + checkoutDate.toLocaleDateString('id-ID') + ')';
                                            }
                                            html += '</div>';
                                        }
                                        html += '</div>';
                                    } else if (occ.employee) {
                                        html += '<div class="mb-1">';
                                        html += '<span class="text-blue-600 font-medium">' + occ.employee.employee_id + '</span> - ';
                                        html += occ.employee.name;
                                        if (occ.employee.department) {
                                            html += ' <span class="text-xs text-gray-500">(' + occ.employee.department.name + ')</span>';
                                        }
                                        html += '</div>';
                                    } else {
                                        console.log("Neither guest nor employee, occ:", occ);
                                    }
                                });
                                html += '</div>';
                                $(container).html(html);
                            } else {
                                $(container).html('<span class="text-gray-400 italic">Tidak ada penghuni</span>');
                            }
                        }
                    },
                    {
                        caption: "Tanggal Check-in",
                        width: 130,
                        allowEditing: false,
                        cellTemplate: function(container, options) {
                            const occupants = options.data.current_occupants || [];
                            if (occupants.length > 0) {
                                let html = '<div class="text-xs">';
                                occupants.forEach(function(occ) {
                                    const date = new Date(occ.check_in_date);
                                    html += '<div class="mb-1">' + date.toLocaleDateString('id-ID') + '</div>';
                                });
                                html += '</div>';
                                $(container).html(html);
                            } else {
                                $(container).text('-');
                            }
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
                    fileName: "Data_Kamar"
                },
                onRowInserted: function(e) {
                    DevExpress.ui.notify("Data kamar berhasil ditambahkan", "success", 3000);
                },
                onRowUpdated: function(e) {
                    DevExpress.ui.notify("Data kamar berhasil diupdate", "success", 3000);
                },
                onRowRemoved: function(e) {
                    DevExpress.ui.notify("Data kamar berhasil dihapus", "success", 3000);
                }
            });

            // Initialize assign button immediately after grid
            console.log("Initializing button...");
            try {
                $("#btnAssignEmployee").dxButton({
                    text: "Assign Karyawan ke Kamar",
                    icon: "add",
                    type: "default",
                    onClick: function() {
                        console.log("Button clicked!");
                        showAssignPopup();
                    }
                });
                console.log("Button initialized successfully");
            } catch(error) {
                console.error("Error initializing button:", error);
            }

            // Function untuk show assign popup
            function showAssignPopup() {
                console.log("showAssignPopup() called");
                try {
                    console.log("Creating formData...");
                    let formData = {
                        room_id: null,
                        is_guest: false,
                        employee_id: null,
                        guest_name: null,
                        guest_purpose: null,
                        guest_duration_days: null,
                        check_in_date: new Date()
                    };

                    console.log("Creating popup element...");
                    const popup = $("<div>").dxPopup({
                    title: "Assign ke Kamar",
                    width: 550,
                    height: 550,
                    showCloseButton: true,
                    shading: true,
                    shadingColor: "rgba(0, 0, 0, 0.5)",
                    container: 'body',
                    visible: false,
                    position: {
                        my: 'center',
                        at: 'center',
                        of: window
                    },
                    onShowing: function(e) {
                        // Reset form data to default state
                        formData.room_id = null;
                        formData.is_guest = false;
                        formData.employee_id = null;
                        formData.guest_name = null;
                        formData.guest_purpose = null;
                        formData.guest_duration_days = null;
                        formData.check_in_date = new Date();
                    },
                    contentTemplate: function(contentElement) {
                        console.log("contentTemplate called, contentElement:", contentElement);
                        const $form = $("<div>").appendTo(contentElement);

                        $form.dxForm({
                            formData: formData,
                                    items: [
                                        {
                                            dataField: "room_id",
                                            label: { text: "Pilih Kamar" },
                                            editorType: "dxSelectBox",
                                            editorOptions: {
                                                dataSource: new DevExpress.data.DataSource({
                                                    loadMode: "raw",
                                                    load: function() {
                                                        return $.getJSON("{{ route('rooms.data') }}");
                                                    }
                                                }),
                                                displayExpr: function(item) {
                                                    if (!item) return "";
                                                    const status = item.occupied_count < item.capacity ? "Tersedia" : "Penuh";
                                                    return item.room_code + " (Kapasitas: " + item.capacity + ", Terisi: " + item.occupied_count + ") - " + status;
                                                },
                                                valueExpr: "id",
                                                searchEnabled: true,
                                                dropDownOptions: {
                                                    container: contentElement
                                                }
                                            },
                                            validationRules: [{ type: "required", message: "Kamar harus dipilih" }]
                                        },
                                        {
                                            dataField: "is_guest",
                                            label: { text: "Tipe Penghuni" },
                                            editorType: "dxSelectBox",
                                            editorOptions: {
                                                dataSource: [
                                                    { value: false, text: "Karyawan" },
                                                    { value: true, text: "Tamu" }
                                                ],
                                                displayExpr: "text",
                                                valueExpr: "value",
                                                value: false,
                                                dropDownOptions: {
                                                    container: contentElement
                                                },
                                                onValueChanged: function(e) {
                                                    const form = $form.dxForm("instance");
                                                    const isGuest = e.value;

                                                    console.log("Tipe penghuni changed to:", isGuest ? "Tamu" : "Karyawan");

                                                    // Show/hide fields based on selection
                                                    form.itemOption("employee_id", "visible", !isGuest);
                                                    form.itemOption("guest_name", "visible", isGuest);
                                                    form.itemOption("guest_purpose", "visible", isGuest);
                                                    form.itemOption("guest_duration_days", "visible", isGuest);

                                                    // Update validation rules based on visibility
                                                    if (isGuest) {
                                                        // Tamu selected: disable employee validation, enable guest validations
                                                        form.itemOption("employee_id", "validationRules", []);
                                                        form.itemOption("guest_name", "validationRules", [{ type: "required", message: "Nama tamu harus diisi" }]);
                                                        form.itemOption("guest_purpose", "validationRules", [{ type: "required", message: "Keperluan harus diisi" }]);
                                                        form.itemOption("guest_duration_days", "validationRules", [{ type: "required", message: "Durasi menginap harus diisi" }]);
                                                        
                                                        formData.employee_id = null;
                                                    } else {
                                                        // Karyawan selected: enable employee validation, disable guest validations
                                                        form.itemOption("employee_id", "validationRules", [{ type: "required", message: "Karyawan harus dipilih" }]);
                                                        form.itemOption("guest_name", "validationRules", []);
                                                        form.itemOption("guest_purpose", "validationRules", []);
                                                        form.itemOption("guest_duration_days", "validationRules", []);
                                                        
                                                        formData.guest_name = null;
                                                        formData.guest_purpose = null;
                                                        formData.guest_duration_days = null;
                                                    }

                                                    // Revalidate form
                                                    form.validate();
                                                }
                                            },
                                            validationRules: [{ type: "required" }]
                                        },
                                        {
                                            dataField: "employee_id",
                                            label: { text: "Pilih Karyawan" },
                                            editorType: "dxSelectBox",
                                            visible: true,
                                            editorOptions: {
                                                dataSource: new DevExpress.data.DataSource({
                                                    loadMode: "raw",
                                                    load: function() {
                                                        return $.getJSON("{{ route('employees.data') }}");
                                                    }
                                                }),
                                                displayExpr: function(item) {
                                                    if (!item) return "";
                                                    const deptName = item.department ? item.department.name : 'N/A';
                                                    return item.employee_id + " - " + item.name + " (" + deptName + ")";
                                                },
                                                valueExpr: "id",
                                                searchEnabled: true,
                                                dropDownOptions: {
                                                    container: contentElement
                                                }
                                            },
                                            validationRules: [{
                                                type: "required",
                                                message: "Karyawan harus dipilih",
                                                reevaluate: true
                                            }]
                                        },
                                        {
                                            dataField: "guest_name",
                                            label: { text: "Nama Tamu" },
                                            visible: false,
                                            editorOptions: {
                                                placeholder: "Masukkan nama tamu"
                                            },
                                            validationRules: [{
                                                type: "required",
                                                message: "Nama tamu harus diisi",
                                                reevaluate: true
                                            }]
                                        },
                                        {
                                            dataField: "guest_purpose",
                                            label: { text: "Keperluan" },
                                            visible: false,
                                            editorOptions: {
                                                placeholder: "Masukkan keperluan tamu"
                                            },
                                            validationRules: [{
                                                type: "required",
                                                message: "Keperluan harus diisi",
                                                reevaluate: true
                                            }]
                                        },
                                        {
                                            dataField: "guest_duration_days",
                                            label: { text: "Durasi Menginap (hari)" },
                                            visible: false,
                                            editorType: "dxNumberBox",
                                            editorOptions: {
                                                min: 1,
                                                max: 90,
                                                placeholder: "Masukkan durasi dalam hari (max 90)"
                                            },
                                            validationRules: [{
                                                type: "required",
                                                message: "Durasi menginap harus diisi",
                                                reevaluate: true
                                            }]
                                        },
                                        {
                                            dataField: "check_in_date",
                                            label: { text: "Tanggal Check-in" },
                                            editorType: "dxDateBox",
                                            editorOptions: {
                                                type: "date",
                                                displayFormat: "dd/MM/yyyy",
                                                min: new Date()
                                            },
                                            validationRules: [{ type: "required", message: "Tanggal check-in harus diisi" }]
                                        }
                                    ]
                                });

                        $("<div>").css("margin-top", "20px").dxButton({
                            text: "Assign",
                            type: "success",
                            width: "100%",
                            onClick: function() {
                                const validation = $form.dxForm("instance").validate();
                                if (!validation.isValid) {
                                    DevExpress.ui.notify("Semua field harus diisi dengan benar", "warning", 3000);
                                    return;
                                }

                                // If guest, skip employee check
                                if (formData.is_guest) {
                                    performAssignment();
                                    return;
                                }

                                // Check if employee already has a room
                                $.ajax({
                                    url: "/employees/" + formData.employee_id,
                                    method: "GET",
                                    success: function(employee) {
                                        // Check if employee has current room
                                        if (employee.current_room) {
                                            // Hide popup first to show confirmation dialog on top
                                            popup.hide();

                                            // Show confirmation dialog
                                            const confirmDialog = DevExpress.ui.dialog.confirm(
                                                "Karyawan " + employee.name + " saat ini sudah menempati kamar " +
                                                employee.current_room.room_code + ". Yakin ingin memindahkan kamar karyawan ini?",
                                                "Konfirmasi Pemindahan"
                                            );

                                            confirmDialog.done(function(dialogResult) {
                                                if (dialogResult) {
                                                    // User clicked "Yes", proceed with assignment
                                                    performAssignment();
                                                } else {
                                                    // User clicked "No", show popup again
                                                    popup.show();
                                                }
                                            });
                                        } else {
                                            // Employee has no room, proceed directly
                                            performAssignment();
                                        }
                                    },
                                    error: function() {
                                        DevExpress.ui.notify("Gagal memeriksa data karyawan", "error", 3000);
                                    }
                                });

                                function performAssignment() {
                                    $.ajax({
                                        url: "{{ route('rooms.assign') }}",
                                        method: "POST",
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        data: {
                                            room_id: formData.room_id,
                                            employee_id: formData.employee_id,
                                            check_in_date: formData.check_in_date.toISOString().split('T')[0]
                                        },
                                        success: function(response) {
                                            DevExpress.ui.notify(response.message, "success", 3000);
                                            popup.hide();
                                            $("#roomGrid").dxDataGrid("refresh");
                                        },
                                        error: function(xhr) {
                                            const message = xhr.responseJSON && xhr.responseJSON.message
                                                ? xhr.responseJSON.message
                                                : "Terjadi kesalahan";
                                            DevExpress.ui.notify(message, "error", 3000);
                                        }
                                    });
                                }
                            }
                        }).appendTo(contentElement);
                    }
                }).dxPopup("instance");

                console.log("Popup created, showing now...");
                console.log("Popup instance:", popup);
                console.log("Popup element:", popup.element());

                // Append popup to body explicitly
                $(popup.element()).appendTo('body');

                popup.show();
                console.log("Popup.show() called");
                console.log("Popup visible?", popup.option("visible"));

                // Force check overlay
                setTimeout(function() {
                    console.log("After show - checking overlay...");
                    console.log("Overlay exists?", $('.dx-overlay-wrapper').length);
                    console.log("Popup container:", popup.element().parent());

                    // Try to make it visible with CSS
                    $(popup.element()).css({
                        'display': 'block',
                        'visibility': 'visible',
                        'z-index': '9999',
                        'position': 'fixed'
                    });
                    $('.dx-overlay-wrapper').css('z-index', '9999');
                }, 100);
            } catch(error) {
                console.error("Error in showAssignPopup():", error);
            }
        }
        });
    </script>
</x-app-layout>
