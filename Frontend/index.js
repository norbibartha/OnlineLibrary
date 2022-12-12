function submitAction() {
    const div = document.getElementById('results');
    if (div !== null) {
        document.body.removeChild(div);
    }

    let authorName = document.getElementById('author').value;

    fetch('../api.php?author=' + authorName, {
        method: 'GET',
        headers: {
            'Content-type': 'application/json; charset=UTF-8'
        }
    })
        .then(response => response.json())
        .then(json => {
            if (json.responseCode !== 200 || json.responseMessage !== 'Success') {
                console.log(json.responseMessage);
                return;
            }

            const books = json.books;

            const div = document.createElement('div');
            div.id = 'results';
            document.body.appendChild(div);
            div.innerHTML = '<h2>Results</h2>';

            const table = document.createElement('table');
            table.className = 'slide-in';

            div.appendChild(table);

            createTableHead(table);

            let i = 1;
            books.forEach((book) => {
                const table = document.createElement('table');
                table.className = 'slide-in';
                table.style.animationDelay = i + 's';
                i++;
                const tr = createTableRow(authorName, book.title);
                table.appendChild(tr);
                div.appendChild(table);
            })
        });
}

function createTableHead(table) {
    const trHead = document.createElement('tr');
    const tdHead1 = document.createElement('td');
    tdHead1.className = 'td-head';
    const node1 = document.createTextNode('Author Name');
    tdHead1.appendChild(node1);
    trHead.appendChild(tdHead1);

    const tdHead2 = document.createElement('td');
    tdHead2.className = 'td-head';
    const node2 = document.createTextNode('Book Title');
    tdHead2.appendChild(node2);
    trHead.appendChild(tdHead2);

    table.appendChild(trHead);
}

function createTableRow(authorName, title) {
    const tr = document.createElement('tr');
    const tdAuthorName = document.createElement('td');
    const authorNode = document.createTextNode(authorName);
    tdAuthorName.appendChild(authorNode);
    tr.appendChild(tdAuthorName);

    const tdBookTitle = document.createElement('td');
    const bookTitleNode = document.createTextNode(title);
    tdBookTitle.appendChild(bookTitleNode);
    tr.appendChild(tdBookTitle);

    return tr;
}