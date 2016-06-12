/**
 * 该文件基于 jquery & vue.js
 *
 * 可以在任何网页插入脚本，然后即可页面生成评论筐，用户即可留言
 */
function Comment(params) {
    this.params = params;
    this._vue = null;
    this.init();
}

Comment.prototype.init = function() {
    Vue.component('reply-item', {
        template: ['<div class="body">',
            '<div class="header">',
                '<span class="author">{{item.username}}</span>',
                '<span class="bullet">•</span>',
                '<span class="time">{{item.created_at}}</span>',
            '</div>',
            '<div>',
                '{{item.content}}',
            '</div>',
            '<div class="footer">',
                '<a href="javascript:;" @click="hideReply=!hideReply">回复</a>',
                '<div v-show="hideReply">',
                    '<textarea class="text-content reply" v-model="content"></textarea>',
                    '<div class="text-ctrl">',
                        '<button type="button" class="btn btn-primary" @click="onReply">回复</button>',
                    '</div>',
                '</div>',
            '</div>',
        '</div>',
        '<ul class="comment-list children" v-show="item.children.length > 0">',
            '<li v-for="(index, child) in item.children">',
                '<reply-item :item="child"></reply-item>',
            '</li>',
        '</ul>'].join(''),
        props : ['item'],
        data  : function() {
            return {
                hideReply : false,
                content   : '',
            }
        },
        methods : {
            onReply : function() {
                console.dir(this.item)
                var that = this;
                var params = {
                    content   : this.content,
                    parent_id : this.item.id,
                    hash      : this.item.hash,
                }
                $.post('/comment/content/reply', params, function(res) {
                    that.$root.onLoad();
                }, 'json')
            }
        }
    })
    this._vue = new Vue({
        el: this.params.el,
        template: [
            '<div class="commentWrap">',
                '<div class="comment-text">',
                    '<div>{{username}}</div>',
                    '<textarea class="text-content" v-model="content"></textarea>',
                    '<div class="text-ctrl">',
                        '<button type="button" class="btn btn-primary" @click="onSend">发言</button>',
                    '</div>',
                '</div>',
                '<ul class="comment-list">',
                    '<li v-for="(index, item) in list">',
                        '<reply-item :item="item"></reply-item>',
                    '</li>',
                '</ul>',
            '</div>'
        ].join(''),
        data : {
            content   : '',
            list      : [],
            hash      : '',
            username  : this.params.username,
        },
        created: function() {
            this.setHash();
        },
        methods : {
            setHash: function() {
                var that = this;
                var params = {
                    url: location.href
                }
                $.getJSON('/comment/content/hash', params, function(res) {
                    that.hash = res;
                    that.onLoad();
                })
            },
            onLoad: function() {
                var that = this;
                $.getJSON('/comment/content/get', {hash: this.hash}, function(res) {
                    that.list = res;
                })
            },
            onSend: function() {
                var that = this;
                var params = {
                    content : this.content,
                    hash    : this.hash,
                }
                $.post('/comment/content/add', params, function(res) {
                    that.content = '';
                    that.onLoad();
                }, 'json')
            }
        }
    });
}
