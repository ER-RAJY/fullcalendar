<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8" />
    <title>الجدول الزمني للحجوزات</title>

    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <style>
        body {
            font-family: 'Tahoma', sans-serif;
            direction: rtl;
            text-align: right;
            background-color: #f8f9fa;
        }

        #calendar {
            max-width: 900px;
            margin: 40px auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        select {
            margin: 10px;
            padding: 5px 10px;
            font-size: 1rem;
        }

        h2 {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <h2>نظام الحجوزات</h2>

    <div style="text-align:center;">
        <!-- اختيار نوع الغرفة -->
        <select id="typeRoomSelect">
            <option value="">-- اختر نوع الغرفة --</option>
            @foreach ($typeRooms as $typeRoom)
                <option value="{{ $typeRoom->id }}">{{ $typeRoom->name }}</option>
            @endforeach
        </select>

        <!-- اختيار الغرفة -->
        <select id="roomSelect">
            <option value="">-- اختر الغرفة --</option>
        </select>

        <!-- اختيار نوع النشاط -->
        <select id="activityType">
            <option value="stay">إقامة</option>
            <option value="conference">مؤتمر</option>
            <option value="meeting">اجتماع</option>
        </select>
    </div>

    <!-- FullCalendar -->
    <div id="calendar"></div>

    <script>
        const typeRooms = @json($typeRooms);

        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');
            const roomSelect = document.getElementById('roomSelect');
            const activityTypeSelect = document.getElementById('activityType');

            const calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'ar',
                initialView: 'dayGridMonth',
                selectable: true,
                editable: true,
                events: function(fetchInfo, successCallback, failureCallback) {
                    const roomId = roomSelect.value;
                    if (!roomId) return successCallback([]);

                    axios.get('/events')
                        .then(res => {
                            const events = res.data.filter(e => e.room_id == roomId);
                            successCallback(events);
                        })
                        .catch(() => failureCallback());
                },
                select: function(info) {
                    const roomId = roomSelect.value;
                    if (!roomId) {
                        alert('المرجو اختيار الغرفة أولاً.');
                        calendar.unselect();
                        return;
                    }

                    axios.get('/check-availability', {
                        params: {
                            room_id: roomId,
                            start_date: info.startStr,
                            end_date: info.endStr,
                            activity_type: activityTypeSelect.value
                        }
                    }).then(response => {
                        if (response.data.available) {
                            const clientName = prompt("اسم الزبون:");
                            if (clientName) {
                                axios.post('/events', {
                                    client_name: clientName,
                                    room_id: roomId,
                                    start_date: info.startStr,
                                    end_date: info.endStr,
                                    activity_type: activityTypeSelect.value
                                }).then(() => {
                                    alert("تم الحجز بنجاح!");
                                    calendar.refetchEvents();
                                }).catch(() => {
                                    alert("حدث خطأ أثناء إنشاء الحجز.");
                                });
                            }
                        } else {
                            alert("الغرفة محجوزة في هذا التاريخ!");
                        }
                    });

                    calendar.unselect();
                },
                eventDrop: function(info) {
                    const event = info.event;
                    axios.put('/events/' + event.id, {
                        start_date: event.startStr,
                        end_date: event.endStr || event.startStr
                    }).then(response => {
                        if (!response.data.success) {
                            alert(response.data.message || "حدث خطأ.");
                            info.revert();
                        }
                    }).catch(() => {
                        alert('الغرفة محجوزة بالفعل في هذه الفترة!');
                        info.revert();
                    });
                },
                eventDidMount: function(info) {
                    info.el.style.backgroundColor = '#198754'; // Bootstrap green
                    info.el.style.color = 'white';
                }
            });

            calendar.render();

            document.getElementById('typeRoomSelect').addEventListener('change', function () {
                const typeId = this.value;
                roomSelect.innerHTML = '<option value="">-- اختر الغرفة --</option>';

                if (typeId) {
                    const selectedType = typeRooms.find(type => type.id == typeId);
                    if (selectedType?.rooms) {
                        selectedType.rooms.forEach(room => {
                            const opt = document.createElement('option');
                            opt.value = room.id;
                            opt.textContent = room.name;
                            roomSelect.appendChild(opt);
                        });
                    }
                }

                calendar.refetchEvents();
            });

            roomSelect.addEventListener('change', () => calendar.refetchEvents());
        });
    </script>

</body>
</html>
