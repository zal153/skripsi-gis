import './bootstrap';

import Alpine from 'alpinejs';
import { DataTable } from 'simple-datatables';
import L from 'leaflet';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.SimpleDataTable = DataTable;
window.L = L;
window.Swal = Swal;

Alpine.start();
