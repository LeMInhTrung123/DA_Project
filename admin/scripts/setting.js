let general_data;
        
let general_s_form = document.getElementById('general_s_form');
let site_title_inp = document.getElementById('site_title_inp');
let site_about_inp = document.getElementById('site_about_inp');

function get_general() {
    if (sessionStorage.getItem('general_data')) {
        let general_data = JSON.parse(sessionStorage.getItem('general_data'));
        document.getElementById('site_title').innerText = general_data.site_title;
        document.getElementById('site_about').innerText = general_data.site_about;
        return;
    }

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/settings_crud.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        general_data = JSON.parse(this.responseText);
        sessionStorage.setItem('general_data', JSON.stringify(general_data));
        document.getElementById('site_title').innerText = general_data.site_title;
        document.getElementById('site_about').innerText = general_data.site_about;
    };

    xhr.send('get_general=1');
}



general_s_form.addEventListener('submit',function(e)
{
    e.preventDefault();
    upd_general(site_title_inp.value,site_about_inp.value);
})


function upd_general(site_title_val, site_about_val) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/settings_crud.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        var myModal = document.getElementById('general-s');
        var modal = bootstrap.Modal.getInstance(myModal);
        modal.hide();

        if (this.responseText == 1) {
            alert('success', 'Lưu thành công!');
            sessionStorage.removeItem('general_data'); // Xóa cache trước khi tải lại
            get_general(); // Tải lại dữ liệu
        } else {
            alert('error', 'Không thay đổi!');
        }
    };

    xhr.send('site_title=' + encodeURIComponent(site_title_val) + '&site_about=' + encodeURIComponent(site_about_val) + '&upd_general');
}


function upd_shutdown(val)
{
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/settings_crud.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {

        if (this.responseText == 1 && general_data.shutdown==0) {
            alert('success', 'Shutdown mode');
            
        } else {
            alert('success', 'Không thay đổi!');
        }
        get_general();
    }


    xhr.send('upd_shutdown='+val);
}
window.onload = function() {    
    get_general();
}

