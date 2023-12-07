let listContainer = document.getElementById('match-pair-list');
let pairs = [];

let taskPlaceholder = document.getElementById('task-placeholder');
let nameInput = document.getElementById('task-name-input');

document.getElementById('add-pair').onclick = ()=>{
    addEmptyPair();
    if(document.getElementById('task-placeholder')){
        document.getElementById('task-placeholder').remove();
    }
}

document.getElementById('download').onclick = function() {
    let text = JSON.stringify(makeJSON());
    document.getElementById('taskJson').value = text;
    document.getElementById('save').click();
    /*let myData = 'data:application/txt;charset=utf-8,' + encodeURIComponent(text);
    this.href = myData;

    let filename = 'task';
    if(nameInput.value != ''){
        filename = nameInput.value;
    }
    this.download = filename + '.txt';*/
}

function makeJSON(){
    let result = { type: "match"};
    result.tasktext = document.getElementById('tasktext').value;
    a = [];
    b = [];
    for (const i of pairs) {
        a.push(i.children[0].value);
        b.push(i.children[1].value);
    }
    result.content = [a,b];
    return result;
}

function addEmptyPair(){
    let newPair = document.createElement('div');
    newPair.className = 'match-pair';
    newPair.innerHTML = '<input type="text" class="input-pair" placeholder="A"><input type="text" class="input-pair" placeholder="B">';
    del_btn = document.createElement('button');
    del_btn.innerHTML = '-';
    del_btn.className = 'del-button';
    del_btn.onclick = ()=>{
        pairs.splice(pairs.indexOf(this.parent));
        newPair.remove();
        if(pairs.length == 0){
            listContainer.append(taskPlaceholder);
        }
        console.log(pairs);
    }
    pairs.push(newPair);
    newPair.append(del_btn);
    listContainer.append(newPair);
}

function makeWordPairs(){
    let results = [];
    for (const i of pairs) {
        let pair = {};
        pair.eng = i.children[0].value;
        pair.ru = i.children[1].value;
        results.push(pair);
    }
    return results;
}