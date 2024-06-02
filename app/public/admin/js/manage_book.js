$(document).ready(function() {
    $('#search').on('keyup', function() {
        let searchValue = $(this).val();
        $.ajax({
            url: '?ajax=1&search=' + searchValue + '&page=1',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                let books = response.books;
                let totalData = response.totalData;
                let tbody = '';
                let no = 1;
                $.each(books, function(index, book) {
                    tbody += `<tr>
                        <td class="px-4 py-2 whitespace-nowrap">${no++}</td>
                        <td class="px-4 py-2 whitespace-nowrap">${book.isbn}</td>
                        <td class="px-4 py-2 whitespace-nowrap">${book.title}</td>
                        <td class="px-4 py-2 whitespace-nowrap">${book.author}</td>
                        <td class="px-4 py-2 whitespace-nowrap">${book.category_id}</td>
                        <td class="px-4 py-2 whitespace-nowrap">${book.publisher}</td>
                        <td class="px-4 py-2 whitespace-nowrap">${book.publish_date}</td>
                        <td class="px-4 py-2 whitespace-nowrap">
                            ${book.cover ? '<img src="' + book.cover + '" alt="Cover" class="w-full object-fill">' : 'No Cover'}
                        </td>
                        <td class="px-4 py-2 text-sm">${book.sinopsis}</td>
                        <td class="px-4 py-2 text-sm">${book.available_copies}</td>
                        <td class="px-4 py-2 whitespace-nowrap">
                            <a href="./edit_book.php?book_id=${book.book_id}" class="bg-yellow-500 text-white px-2 py-2 rounded"><span><i class="fa-regular fa-pen-to-square mx-2"></i></span> Edit</a>
                            <button data-book-id="${book.book_id}" class="delete-book-btn bg-red-500 text-white px-2 py-2 rounded"><span><i class="fa-solid fa-trash-can mx-2" style="color: #ffffff;"></i></span> Delete</button>
                        </td>
                    </tr>`;
                });
                $('#bookTableBody').html(tbody);

                // Update pagination
                let totalPages = Math.ceil(totalData / 3);
                let pagination = '';
                for (let i = 1; i <= totalPages; i++) {
                    pagination += `<a href="?page=${i}&search=${searchValue}" class="px-3 py-1 border rounded mx-1">${i}</a>`;
                }
                $('#pagination').html(pagination);
            }
        });
    });

    $(document).on('click', '.delete-book-btn', function(event) {
        event.preventDefault();
        const bookId = $(this).data('book-id');

        Swal.fire({
            title: 'Apakah kamu yakin?',
            text: "Kamu tidak bisa mengembalikan data ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus saja!'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = $('<form>', {
                    'method': 'POST',
                    'action': 'delete_book.php'
                }).append($('<input>', {
                    'type': 'hidden',
                    'name': 'book_id',
                    'value': bookId
                }));
                $('body').append(form);
                form.submit();
            }
        });
    });

    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const message = urlParams.get('message');

    if (status && message) {
        Swal.fire({
            icon: status === 'success' ? 'success' : 'error',
            title: status === 'success' ? 'Success' : 'Error',
            text: message
        });
    }
});
