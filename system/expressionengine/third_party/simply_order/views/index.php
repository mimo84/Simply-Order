<SCRIPT language="javascript">
    function addRow(tableID) {
 
	var table = document.getElementById(tableID);
 
	var rowCount = table.rows.length;
	var row = table.insertRow(rowCount);
 
	var cell1 = row.insertCell(0);
	var element1 = document.createElement("input");
	element1.type = "checkbox";
	cell1.appendChild(element1);
 
	var cell2 = row.insertCell(1);
	cell2.innerHTML = rowCount + 1;
 
	var cell3 = row.insertCell(2);
	var element2 = document.createElement("input");
	element2.type = "text";
	cell3.appendChild(element2);
 
    }
 
    function deleteRow(tableID) {
	try {
            var table = document.getElementById(tableID);
            var rowCount = table.rows.length;
 
            for(var i=0; i<rowCount; i++) {
                var row = table.rows[i];
                var chkbox = row.cells[0].childNodes[0];
                if(null != chkbox && true == chkbox.checked) {
                    table.deleteRow(i);
                    rowCount--;
                    i--;
                }
 
            }
	}catch(e) {
	    alert(e);
	}
    }
 
</SCRIPT>
<h1>Simply Order - Entries Order</h1>

<?php echo form_open($form_action); ?>
<INPUT type="button" value="Add Row" onclick="addRow('dataTable')" />
 
    <INPUT type="button" value="Delete Row" onclick="deleteRow('dataTable')" />
 
    <TABLE id="dataTable" width="350px" border="1">
        <TR>
            <TD><INPUT type="checkbox" name="chk"/></TD>
            <TD> 1 </TD>
            <TD> <INPUT type="text" /> </TD>
        </TR>
    </TABLE>
    
    <?php echo form_submit('submit', 'submit', 'class="submit"')?>
    
    <?php echo form_close(); ?>