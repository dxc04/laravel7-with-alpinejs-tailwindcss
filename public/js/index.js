function csvData() {
    return {
        active: 'parse-section',
        fileName: '',
        header: [],
        rows: [],
        dataList: [],
        emailCol: null,
        subject: null,
        message: null,
        isShowColMenu: false,
        async parse() {
            var fileUpload = document.getElementById("fileUpload");
            var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.csv|.txt)$/;
            if (regex.test(fileUpload.value.toLowerCase())) {
                this.rows = await this.paparse(fileUpload.files[0], { skipEmptyLines: true });
                this.header = Object.keys(this.rows[0]);
                this.rows.pop();
                this.parseEmail(this.rows[0]);
            } else {
                alert("Please upload a valid CSV file.");
            }
        },
        paparse(file) {
            return new Promise((resolve, reject) => {
                Papa.parse(file, {
                  header: true,
                  complete (results) {
                    resolve(results.data)
                  },    
                  error (err) {
                    reject(err)
                  }
                })
            })
        },
        generate() {
            if (!(this.subject && this.message)) {
                alert("Subject and message templates must not be empty.");
            }

            const subjectTpl = Handlebars.compile(this.subject);
            const messageTpl = Handlebars.compile(this.message);
            this.dataList = this.rows.reduce((dRows, row, index) => {
                const newRow = row;
                newRow['subject'] = subjectTpl(row);
                newRow['message'] = messageTpl(row);
                dRows.push(row);
                return dRows;
            }, []);
            this.active = 'table-section'
        },
        parseEmail(row)
        {
            for (const key in row) {
                if(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(row[key])) {
                    this.emailCol = key;
                    break;
                }
            }
            if (!this.emailCol) {
                this.active = 'parse-section';
                this.fileName = '';
                alert('No email column detected! Make sure your csv has email column.');
            }
            else {
                this.active = 'generate-section';
            }
        }
    }
}