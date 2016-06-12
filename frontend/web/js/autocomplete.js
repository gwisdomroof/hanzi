/**
 *  @author Eugene Terentev <eugene@terentev.net>
 */
$(function() {
	Vue.component('check-child', {
		template: '#check-tmpl',
		data: function () {
			return {
				bushou_list  : HANZI_BUSHOU,
				struct_list  : HANZI_STRUCT,
				check_struct : '',
				check_bushou : [],
				show_struct  : false,
				show_bushou  : false,
				filterBihua  : '',
				bushou       : [],
				check_index  : -1,
				input_queue  : [], 
			}
		},
		computed : {
			one_text : function() {
				return this.check_struct+this.check_bushou.join('');
			}
		},
		watch : {
			filterBihua : function(val, oldVal) {
				if (val) {
					this.bushou.splice(0);
					if (val[0] == 'v') {
						for(var bs in this.bushou_list) {
							var pattern = this.filterBihua.substr(1);
							var re = new RegExp(pattern);
							var _bihua = this.bushou_list[bs];
							if (re.test(_bihua)) {
								this.bushou.push(bs)
							}
						}
					}
					
				}
			}
		},
		methods : {
			checkStruct : function(item) {
				this.check_struct = item;

				this.show_struct = false;
				this.show_bushou = true;
			},
			checkBushou : function(item) {
				this.check_bushou.push(item);
				this.bushou.splice(0);
				this.filterBihua = '';
			},
			onDown : function(e) {
				console.log(e.keyCode)
				if (e.keyCode == 85 && this.filterBihua[0] == 'u') {
					this.show_struct = true;
				}
				if (e.keyCode == 13) {
					if (this.filterBihua[0] == 'u') {
						var i = this.filterBihua.substr(1)
						i = parseInt(i) - 1
						if (/\d+/.test(i) && this.struct_list[i]) {
							var val = this.struct_list[i];
							this.check_struct = val;
							this.filterBihua = '';
							
							this.show_struct = false;
							this.show_bushou = true;
						}
					}
				}
				if (e.keyCode > 64 && e.keyCode < 91) { // 字母
					this.input_queue.push(e.keyCode);
				}

				if (e.keyCode == 32 && this.filterBihua != '') {
					if (this.input_queue.length > 0) {
						for (var i in this.filterBihua) {
							this.check_bushou.push(this.filterBihua[i]);
						}
						this.input_queue.splice(0);
						this.filterBihua = '';
					}
				}
				if (e.keyCode == 8) { // 退格
					if (this.input_queue.length == 0) {
						this.check_bushou.pop();
					}
				}
				if (e.keyCode == 37 || e.keyCode == 38) {
					this.check_index--;
					if (this.check_index < 0) {
						this.check_index = this.struct_list.length - 1;
					}
				}
				if (e.keyCode == 39 || e.keyCode == 40) {
					this.check_index++;
					if (this.check_index == this.struct_list.length) {
						this.check_index = 0;
					}
				}
				if (e.keyCode == 13) {
					if (this.show_struct) {
						//this.checkStruct();
					}
				}
			}
		}
	})
	var App = new Vue({
		el : '#app',
	});
})