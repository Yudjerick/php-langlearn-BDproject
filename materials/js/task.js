let selected = null;
let selectedLeft = true;

let task1 = {"type":"match","tasktext":"","content":[["word","mouth"],["слово","рот"]]}
var task2 = { type: "match", tasktext: "Match words and translations", content: [["general","generous","genetic"],["основной","щедрый","генетический"]]}
var task3 = { type: "match", tasktext: "Match words and translations", content: [["brave","brain","bread","bird","break","beard"],["смелый","мозг","хлеб","птица","перерыв","борода"]]}
var task4 = { type: "match", tasktext: "Match words and their meanings", content: [["ergonomics","economics","etymology"],["the study of people's efficiency in their working environment","the branch of knowledge concerned with the production, consumption, and transfer of wealth","the history of a linguistic form (such as a word) shown by tracing its development since its earliest recorded occurrence in the language where it is found, by tracing its transmission from one language to another, by analyzing it into its component parts, by identifying its cognates in other languages, or by tracing it and its cognates to a common ancestral form in an ancestral language"]]}
let task5 = { type: 'order',tasktext: 'Put words in correct order', content: ['Have','you','ever','been','in','Paris','?']};

let taskContainer = document.querySelector('.task-container');

let taskJson = JSON.parse(document.getElementById('taskJson').innerHTML);
loadTask(taskJson, taskContainer);

function makeRandomMatchTask(pairCount = 8){
    let numbersAdded = [];
    let allMatchPairs = JSON.parse(localStorage.getItem('matchPairs'));
    let animalsChekbox = document.getElementById('animals');
    let psyhologyChekbox = document.getElementById('psyhology');
    let checkBoxes = [animalsChekbox, psyhologyChekbox];

    let matchPairs = [];
    if(animalsChekbox.checked){
        for (const i of allMatchPairs.animals) {
            matchPairs.push(i);
        }
    }
    if(psyhologyChekbox.checked){
        for (const i of allMatchPairs.psyhology) {
            matchPairs.push(i);
        }
    }
    
    let task = {"type":"match", "tasktext": "Match words and translations"}
    let content = [[],[]];
    for(let i = 0; i < pairCount; i++){
        let rand = randomInt(matchPairs.length-1);
        while(numbersAdded.includes(rand)){
            rand = randomInt(matchPairs.length-1);
        }
        content[0].push(matchPairs[rand].eng);
        content[1].push(matchPairs[rand].ru);
        numbersAdded.push(rand);
    }
    task.content = content;
    return task;
}

function makeRandomOrderTask(){
    let task = { type: 'order',tasktext: 'Put words in correct order'};
    let sentences = JSON.parse(localStorage.getItem('orderTasks'));
    task.content = sentences[randomInt(sentences.length - 1)];
    return task;
}

function randomInt(max) {
    return Math.floor(Math.random() * max);
}

function readFile(input){
    let reader = new FileReader();
    reader.readAsText(input.files[0]);
    reader.onload = ()=>{
        loadTask(JSON.parse(reader.result), document.querySelector('.task-container'));
        return reader.result;
    }
}

function hideLoadButtons(){
    for (let i of loadButtons) {
        i.style.visibility = 'hidden';
    }
}

function loadTask(task, container = document.body) {
    container.innerHTML = '';
    switch (task.type) {
        case 'test':
            loadTestTask(task, container);
            break;
        case 'match':
            loadMatchTask(task, container);
            break;
        case 'order':
            loadOrderTask(task,container);
        default:
            break;
    }
}

function loadTestTask(task, container){
    let div = document.createElement('div');
    div.className = "test-task";
    var innerStr = "<p>" + task.tasktext + "</p>";
    for (var i = 0; i < task.content.length; i++) {
        innerStr += '<p><input type="radio" name="a" value="'+ task.content[i] +'">' + task.content[i] + '</input></p>';
    }
    innerStr += '<input type="submit" id="button">';
    div.innerHTML = innerStr;
    container.append(div);
    let button = document.querySelector('#button');
    let radios = document.querySelectorAll('input[type="radio"]');
    button.addEventListener('click', function() {
        for (let radio of radios) {
            if (radio.checked) {
                if(radio.value == task.answer){
                    div.style.backgroundColor = "lightgreen";
                    //div.innerHTML += "<p>Correct!</p>";
                }
                else{
                    div.style.backgroundColor = "lightcoral";
                    //div.innerHTML += "<p>Wrong!</p>";
                }
            }
        }
    });
}

function loadMatchTask(task, container){
    let svgContainer = document.querySelector('svg');
    if(svgContainer!=null){
        svgContainer.remove();
    }
    svgContainer = document.createElementNS('http://www.w3.org/2000/svg','svg');
    document.body.prepend(svgContainer);
    let div = document.createElement('div');
    let taskText = document.createElement('p');
    taskText.innerHTML = task.tasktext;
    taskText.className = 'task-text';
    let taskBorder = document.createElement('div');
    taskBorder.className = "matchborder";
    taskBorder.prepend(taskText);
    div.append(taskBorder);

    let buttons = [];
    let lines = [];
    let connections = [];
    let joinedButtons = [];

    window.addEventListener('resize', function(){
        for(var line of lines){
            line.remove();
        }
        for(let i = 0; i < joinedButtons.length; i+=2){
            rejoinWords(joinedButtons[i],joinedButtons[i+1]);
        }
        function rejoinWords(from,to){
            connections[task.content[0].indexOf(from.innerHTML)] = to.innerHTML;
            lines.push(drawCurveSVG(from,to,container,'#350066'));
            from.className = "matchelem matchelemjoint";
            from.disabled = true;
            to.className = "matchelem matchelemjoint";
            to.disabled = true;
        }
    })

    container.addEventListener('scroll',()=>{
        for(var line of lines){
            line.remove();
        }
        for(let i = 0; i < joinedButtons.length; i+=2){
            rejoinWords(joinedButtons[i],joinedButtons[i+1]);
        }
        function rejoinWords(from,to){
            connections[task.content[0].indexOf(from.innerHTML)] = to.innerHTML;
            lines.push(drawCurveSVG(from,to,container,'#350066'));
            from.className = "matchelem matchelemjoint";
            from.disabled = true;
            to.className = "matchelem matchelemjoint";
            to.disabled = true;
        }
    })

    for(let i = 0; i < task.content[0].length; i++){
        connections.push("");
    }

    answers = [...task.content[1]];
    answers.sort(()=>Math.random()-0.5)

    for(let i = 0; i < task.content[0].length; i++){
        let row = document.createElement('div');
        row.className = "matchrow";
        taskBorder.append(row);

        let matchElem = document.createElement('button');
        matchElem.className = "matchelem";
        matchElem.innerHTML = task.content[0][i];
        row.append(matchElem);
        buttons.push(matchElem);

        let matchElem2 = document.createElement('button');
        matchElem2.className = "matchelem";
        matchElem2.innerHTML = answers[i];
        row.append(matchElem2);
        buttons.push(matchElem2);
    }

    for(let i = 0; i < buttons.length; i++){
        button = buttons[i];
        if(i%2 == 0){
            button.addEventListener('click',function(event){
                if(!selected){
                    selectedLeft = true;
                    selected = event.currentTarget;
                    selected.className = "matchelem matchelemselected";
                }
                else{
                    if(!selectedLeft){
                        joinWords(event.currentTarget, selected);
                    }
                }
            })
        }
        else{
            button.addEventListener('click',function(event){
                if(!selected){
                    selectedLeft = false;
                    selected = event.currentTarget;
                    selected.className = "matchelem matchelemselected";
                }
                else{
                    if(selectedLeft){
                        joinWords(selected, event.currentTarget);
                    }
                }
            })
        }
    }

    function joinWords(from,to){
        connections[task.content[0].indexOf(from.innerHTML)] = to.innerHTML;
        lines.push(drawCurveSVG(from,to,container,'#350066'));
        joinedButtons.push(from);
        joinedButtons.push(to);
        from.className = "matchelem matchelemjoint";
        from.disabled = true;
        to.className = "matchelem matchelemjoint";
        to.disabled = true;
        selected = null;
    }
    
    let clearBtn = document.createElement('button');
    clearBtn.id = 'clear-btn';
    clearBtn.innerHTML = "Clear connections";
    clearBtn.style.zIndex = 2;
    clearBtn.addEventListener('click',function(){
        for(let line of lines){
            line.remove();
        }
        for(let button of buttons){
            button.disabled = false;
            button.className = "matchelem";
            button.style.backgroundColor = "white";
        }
        joinedButtons = [];
        connections = [];
        for(let i = 0; i < task.content[0].length; i++){
            connections.push("");
        }
    });
    
    taskBorder.append(clearBtn);

    let checkBtn = document.createElement('button');
    checkBtn.style.zIndex = 4;
    checkBtn.innerHTML = "Check";
    checkBtn.addEventListener('click',function(){
        for(let i = 0; i < task.content[0].length; i++){
            if(task.content[1][i] == connections[i]){
                buttons[i*2].style.backgroundColor = "lightgreen";
            }
            else{
                buttons[i*2].style.backgroundColor = "lightcoral";
            }
        }
    });
    taskBorder.append(checkBtn);
    
    container.append(div);
}

function loadOrderTask(task, container)
{
    let orderedElements = [];
    let div = document.createElement('div');
    let taskText = document.createElement('p');
    taskText.innerHTML = task.tasktext;
    taskText.className = 'task-text';
    let taskBorder = document.createElement('div');
    taskBorder.className = "order-task-container";
    let taskBorder2 = document.createElement('div');
    taskBorder2.className = "order-task-dropzone";
    taskBorder2.id = "drop-zone";
    div.prepend(taskText);
    div.append(taskBorder2);
    div.append(taskBorder);
    taskBorder2.onwheel = (e) => {
        e.preventDefault();
        e.target.scrollLeft += e.deltaY;
    }
    let checkBtn = document.createElement('button');
    checkBtn.innerHTML = "Check";
    div.append(checkBtn);
    checkBtn.onclick = (e)=>{
        for(let i = 0; i < orderedElements.length; i++){
            if(orderedElements[i].innerHTML == task.content[i]){
                orderedElements[i].style.backgroundColor = "lightgreen";
            }
            else{
                orderedElements[i].style.backgroundColor = "lightcoral";
            }
        }
    }
    let resetBtn = document.createElement('button');
    resetBtn.innerHTML = "Reset";
    div.append(resetBtn);
    resetBtn.onclick = (e)=>{
        for(let i = 0; i < orderedElements.length; i++){
            orderedElements[i].style.backgroundColor = "white";
            taskBorder.append(orderedElements[i]);
        }
        orderedElements = [];
    }
    taskBorder2.ondragover = allowDrop;
    taskBorder2.ondrop = drop;

    function allowDrop(ev) {
        ev.preventDefault();
        let data = ev.dataTransfer.getData("text"); 
    }
      
    function drag(ev) {
        ev.dataTransfer.setData("text", ev.target.id);
    }
      
    function drop(ev) {
        ev.preventDefault();
        
        let node = null;
        var data = ev.dataTransfer.getData("text");
        let item = document.getElementById(data);
        if(orderedElements.length == 0){
            ev.target.prepend(item);
            orderedElements.unshift(item);
            return;
        }
        if(orderedElements.includes(item)){
            console.log(item);
            orderedElements.splice(orderedElements.indexOf(item),1);
        }
        if(orderedElements.length > 0){
            console.log(ev.pageX);
            for(let i of orderedElements){
                if (ev.pageX > i.getBoundingClientRect().left + i.getBoundingClientRect().width/2){
                    node = i;
                } 
            }
        }
        if(node != null){
            node.after(item);
            orderedElements.splice(orderedElements.indexOf(node)+1, 0, item);
            console.log(orderedElements);
        }
        else{
            orderedElements[0].before(item);
            orderedElements.unshift(item);
            console.log(orderedElements);
        }
        
    }

    function touch(ev){
        let item = ev.target;
        let dropzone = document.getElementById('drop-zone');
        dropzone.append(item);
        orderedElements.push(item);
    }

    answers = [...task.content];
    answers.sort(()=>Math.random()-0.5)
    
    for(let i = 0; i < answers.length; i++){
        let orderElem = document.createElement('button');
        orderElem.className = "order-elem";
        orderElem.id = 'order-elem-' + answers[i] + i;
        orderElem.innerHTML = answers[i];
        orderElem.draggable = true;
        orderElem.ondragstart = drag;
        orderElem.ontouchend = touch;
        taskBorder.append(orderElem);
    }
    container.append(div);
}



function drawLine(from,to,container,color = '#f2f7ff'){
    var canvas = document.createElement('canvas');
    canvas.width = document.body.clientWidth;
    canvas.height = document.body.clientHeight;
    canvas.style.position = "absolute";
    canvas.style.top = 0;
    canvas.style.left = 0;
    const ctx = canvas.getContext('2d');
    var boxFrom = from.getBoundingClientRect();
    var boxTo = to.getBoundingClientRect();
    var pointFrom = {x:boxFrom.right, y:boxFrom.top + boxFrom.height/2};
    var pointTo = {x:boxTo.left, y:boxTo.top + boxTo.height/2};
    ctx.beginPath();       
    ctx.moveTo(pointFrom.x, pointFrom.y);    
    ctx.lineTo(pointTo.x, pointTo.y);
    ctx.strokeStyle = color;
    ctx.lineWidth = 2;
    ctx.stroke();
    
    container.append(canvas);
    return canvas;
}

function drawLineSVG(from,to,container,color = '#f2f7ff'){
    let svg = document.querySelector("svg");
    var boxFrom = from.getBoundingClientRect();
    var boxTo = to.getBoundingClientRect();
    var pointFrom = {x:boxFrom.right, y:boxFrom.top + boxFrom.height/2};
    var pointTo = {x:boxTo.left, y:boxTo.top + boxTo.height/2};
    let line = document.createElementNS('http://www.w3.org/2000/svg','line');
    svg.append(line);
    line.setAttribute('x1',pointFrom.x);
    line.setAttribute('y1',pointFrom.y);
    line.setAttribute('x2',pointTo.x);
    line.setAttribute('y2',pointTo.y);
    line.style = `stroke:${color};stroke-width:2`;
    return line;
}

function drawCurveSVG(from,to,container,color = '#350066'){
    let svg = document.querySelector("svg");
    var boxFrom = getCoords(from);
    var boxTo = getCoords(to);
    var pointFrom = {x:boxFrom.right, y:boxFrom.top + boxFrom.height/2};
    var pointTo = {x:boxTo.left, y:boxTo.top + boxTo.height/2};
    let path = document.createElementNS('http://www.w3.org/2000/svg','path');
    svg.append(path);
    let distanceX = pointTo.x - pointFrom.x;
    let cx1 = pointFrom.x + distanceX/5;
    let cx2 = pointTo.x - distanceX/5;
    path.setAttribute('d',`M ${pointFrom.x}, ${pointFrom.y} C ${cx1},
     ${pointFrom.y} ${cx2}, ${pointTo.y} ${pointTo.x}, ${pointTo.y}`);
    path.style = `stroke:${color};stroke-width:2;fill:none`;
    return path;
}

function getCoords(elem) {
    let box = elem.getBoundingClientRect(); 
    return {
      top: Number(box.top + window.pageYOffset),
      right: Number(box.right + window.pageXOffset),
      bottom: Number(box.bottom + window.pageYOffset),
      left: Number(box.left + window.pageXOffset),
      height: box.height,
      width: box.width,
    };
  }