<template>


    <div>

        <!--Show Task-->

        <div class="modal fade" id="modal-task">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span></button>
                        <h4 class="modal-title">Task information</h4>
                    </div>
                <div class="modal-body">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><b v-html="taskForEdit.task.name"></b></h3>
                        </div>

                            <div v-if="taskForEdit.editing == false" class="box-body">

                                <div class="form-group">
                                    <label>Task ID</label>
                                    <td type="text" class="form-control" v-html="taskForEdit.task.id"></td>
                                </div>

                                <div class="form-group">
                                    <label>Owner</label>
                                    <td type="text" class="form-control" v-html="taskForEdit.owner"></td>
                                </div>
                                <div class="form-group">
                                    <label>Task name</label>
                                    <td type="text" class="form-control" v-html="taskForEdit.task.name"></td>
                                    <!--<input readonly >-->
                                </div>
                                <div class="form-group">
                                    <label>Completed</label>
                                    <td type="text" class="form-control" v-html="taskForEdit.task.completed"></td>

                                </div>

                                <div class="form-group">
                                    <label>Description</label>
                                    <td type="text" class="form-control" v-html="taskForEdit.task.description"> </td>
                                </div>
                            </div>
                            <!-- /.box-body -->


                    </div>
                </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
        </div>



        <widget :loading="loading">
        <p slot="title">Tasques new</p>
        <div v-cloak>

            <table class="table table-bordered table-hover" dusk="tasks">
                <tbody><tr>
                    <th style="width: 10px">#</th>
                    <th class="ellipsis">Task</th>
                    <th style="width: 150px">Owner</th>
                    <th style="width: 150px">Completed</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
                <tr v-for="(task, index) in filteredTasks" dusk="tasks" id="tasks">
                    <td>{{index +1 }}</td>
                    <td v-if="editor == 'medium-editor'" class="ellipsis" @click="takeTaskForEdit(task, index)">

                        <medium-editor :text='task.name' v-on:edit="recordingNewName"></medium-editor>

                    </td>


                    <td v-if="editor == 'quill'" class="ellipsis" @click="takeTaskForEdit(task, index, 'name')">

                        <div v-if="index == taskForEdit.index && taskForEdit.editing == true && taskForEdit.field == 'name'">

                            <quill-editor v-model="taskForEdit.task.name"> </quill-editor>

                        </div>

                        <div v-if="taskForEdit.index != index || taskForEdit.field == 'description' || taskForEdit.field == 'user'" v-html="task.name"></div>

                    </td>


                    <td v-if="taskForEdit.index == index && taskForEdit.field == 'user'">
                        <select @change="takeTaskForEdit(task, index, 'user')" v-model="task.user_id">
                            <option v-for="user in users" :value="user.id">{{user.name}}</option>
                        </select>
                    </td>

                    <td v-else @click="takeTaskForEdit(task, index, 'user')">
                        <!--<select class="prova" @change="takeTaskForEdit(task, index, 'user')" v-model="task.user_id">-->
                            <!--<option v-for="user in users" :value="user.id">{{user.name}}</option>-->
                        <!--</select>-->
                        {{users[task.user_id-1].name}}
                    </td>


                    <td class="align-center"> <toggle-button :value="task.completed" @change="isCompletedTask(task)" v-model="task.completed"/> </td>
                    <!--<td class="description" @click="takeTaskForEdit(task, index)" v-html="task.description" data-toggle="modal" data-target="#modal-description"></td>-->

                    <td v-if="editor == 'medium-editor'" class="ellipsis" @click="takeTaskForEdit(task, index, 'description')">


                        <medium-editor :text='task.description' v-on:edit="recordingNewDescription"></medium-editor>

                    </td>


                    <td v-if="editor == 'quill'" class="ellipsis" @click="takeTaskForEdit(task, index, 'description')">

                        <div v-if="index == taskForEdit.index && taskForEdit.editing == true && taskForEdit.field == 'description'">

                            <quill-editor v-model="taskForEdit.task.description"> </quill-editor>

                        </div>

                        <div v-if="taskForEdit.index != index || taskForEdit.field == 'name' || taskForEdit.field == 'user'" v-html="task.description"></div>

                    </td>


                    <td class="action">
                        <div class="align-center btn-group">
                            <button v-if="index == taskForEdit.index && taskForEdit.editing == true " type="button" @click="saveTask(task)" class="btn btn-default" alt="Save Task">
                                <i class="fa fa-floppy-o"></i>
                            </button>
                            <button @click="btnShowTask(task)" type="button" data-toggle="modal" data-target="#modal-task" class="btn btn-success" dusk="show">
                                <i class="fa fa-eye"></i>
                            </button>
                            <!--<button @click="btnEditTask(task, index)" data-toggle="modal" data-target="#modal-task" class="btn btn-info">-->
                                <!--<i class="fa fa-edit"></i>-->
                            <!--</button>-->
                            <button @click="btnDeleteTask(task)" type="button" class="btn btn-danger">
                                <i class="fa fa-trash-o"></i>
                            </button>
                        </div>
                    </td>


                </tr>

                </tbody>
            </table>


            <div class="form-group has-feedback" :class="{'has-error': form.errors.has('user_id')}">
                <label for="user_id">User</label>
                <transition name="fade">
                    <span v-text="form.errors.get('user_id')" v-if="form.errors.has('user_id')" class="help-block"></span>
                </transition>
                <users @select="userSelected" id="user_id" name="user_id" v-model="form.user_id" :value="form.user_id"></users>
            </div>

            <div class="form-group has-feedback" :class="{'has-error': form.errors.has('name')}">
                <label for="name">Task Name</label>
                <transition name="fade">
                    <span v-text="form.errors.get('name')" v-if="form.errors.has('name')" class="help-block"></span>
                </transition>
                <input @input="form.errors.clear('name')" class="form-control" type="text" v-model="form.name" id="name" name="name">
            </div>

            <div class="form-group has-feedback" :class="{'has-error': form.errors.has('description')}">
                <label for="description">Task Description</label>
                <transition name="fade">
                    <span v-text="form.errors.get('description')" v-if="form.errors.has('description')" class="help-block"></span>
                </transition>
                <input @input="form.errors.clear('description')" class="form-control" type="text" v-model="form.description" id="description" name="description" @keydown.enter="addTask">
            </div>

            <p>
                <button :disabled="form.submitting || form.errors.any()" id="add" @click="addTask" class="btn btn-primary">
                    <i class="fa fa-refresh fa-spin fa-lg" v-if="form.submitting"></i>
                    Afegir Tasca
                </button>
            </p>

            <div class="btn-group">
                <button @click="show('all')" :class="{active: this.filter =='all'}"> All</button>
                <button id="completed" dusk="completed" @click="show('completed')" :class="{active: this.filter =='completed'}">Completed</button>
                <button @click="show('pending') " :class="{active: this.filter =='pending'}">Pending</button>

            </div>

            <div slot="footer">

                <h2>Tasques pendents: </h2>

                <ul>
                    {{this.pendingTaskCounter}}
                </ul>

            </div>

        </div>


        </widget>

        <message title="Message" message="" color="info"></message>


    </div>
</template>

<style src="quill/dist/quill.snow.css"></style>
<style src="medium-editor/dist/css/medium-editor.min.css"></style>
<style src="medium-editor/dist/css/themes/default.min.css"></style>

<style>

    [v-cloak] { display: none; }
    li.completed {
        text-decoration: line-through;
    }

    li.active {
        background-color: #cdffe4;
    }


    .fade-enter-active, .fade-leave-active {
        transition: opacity 0.5s ease;
    }

    .fade-enter, .fade-leave-to{
        opacity: 0;
    }
    .ellipsis{

        max-width: 100px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;

    }

    .name {

        width: 70px;
    }

    .action{

        width: 150px;

    }

    .align-center{

        display: flex;
            justify-content: center;
    }

</style>


<script>

  import Users from './Users'
  import Form from 'acacha-forms'

  //editors
  import Quill from 'quill'
  import { quillEditor } from 'vue-quill-editor'
  import MediumEditor from 'medium-editor'
  import editor from 'vue2-medium-editor'

  // require styles
  import 'quill/dist/quill.core.css'
  import 'quill/dist/quill.snow.css'
  import 'quill/dist/quill.bubble.css'


  //importo fitxer de conf. per escollir l'editor
  import { config } from '../config/tasks.js'


  // visibility filters
  var filters = {
    all: function (tasks) {

      return tasks
    },
    pending : function (tasks) {
      return tasks.filter(function (task) {
        return !task.completed

      })
    },
    completed: function (tasks) {
      return tasks.filter(function (task) {
        return task.completed
      })
    }
  }

  const API_URL = '/api/v1/tasks';
  const API_URL_USERS = '/api/v1/users';
  const LOCAL_STORAGE_KEY = 'TASKS';


  import { wait } from './utils.js'


  export default {
    components: { Users, quillEditor, 'medium-editor': editor },

    data() {
      return {
        loading: false,
        tasks: [],
        filter: 'all',
        editor: config.editor,
        taskForEdit: {
          index: -1,
          task: [],
          editing: false,
          field: '',
          user: [],
          owner: '',
        },

        users: [],
        modifiedDescription: '',
        modifiedName: '',

        form: new Form({user_id:'', name: '', id: '', description: ''})
      }
    },

    computed: {
      filteredTasks() {

        return filters[this.filter](this.tasks)
      },
      pendingTaskCounter: function(){
        return filters.pending(this.tasks).length

      }

    },

    methods: {

      show(filter){

        this.filter = filter
      },

      userSelected(user) {

        this.form.user_id = user.id;
      },

      addTask() {

        let id = '';
        let url = API_URL
        this.form.post(url).then((response) =>  {
            id = response.data.id;
          this.tasks.push({ name : this.form.name, id: id, user_id: this.form.user_id, description: this.form.description, completed : false})
          this.form.name=''
          this.form.description=''


        }).catch((error) => {
          flash(error.message)
        })

      },

      takeTaskForEdit(task, index, field){

        this.taskForEdit.task = task;
        this.taskForEdit.index = index;
        this.taskForEdit.editing = true;
        this.taskForEdit.field = field;
        this.taskForEdit.user = this.users[task.user_id];

        if(this.modifiedDescription == '' && this.modifiedName == ''){

          this.modifiedDescription = task.description;

          this.modifiedName = task.name;

        }
      },

      recordingNewDescription(task) {

        this.modifiedDescription = task.api.origElements.innerHTML;

      },

      recordingNewName(task) {

        this.modifiedName = task.api.origElements.innerHTML;
      },

      prova(task){
        console.log(task);
      },

      saveTask(task) {
        var pos = this.tasks.indexOf(task);
        var description = '';
        var name = '';
        var owner = this.users[task.user_id-1];
        let url = '/api/v1/description-tasks/'+task.id;

        (this.editor == '') ? name = task.name : (this.editor == 'medium-editor') ? name = this.modifiedName : name = this.taskForEdit.task.name;

        (this.editor == '') ? description = task.description : (this.editor == 'medium-editor') ? description = this.modifiedDescription : description = this.taskForEdit.task.description;

        axios.put(url, {name: name, description: description, user_id: owner.id }).then((response) =>  {

          this.tasks[pos].name = name;
          this.tasks[pos].description = description;
          this.tasks[pos].user_id = owner.id;

        }).catch((error) => {

            flash(error.message)

        }).then(()=>{
          this.taskForEdit.task = [];
          this.taskForEdit.index = -1;
          this.taskForEdit.editing = false;
          this.taskForEdit.field = '';
          this.$emit('loading',false)

        })

      },
      isCompletedTask(task){

        let url = '/api/v1/completed-tasks/'+task.id
        var pos =   this.tasks.indexOf(task);

        axios.put(url, {completed: task.completed }).then((response) =>  {

            this.tasks[pos].completed = task.completed;

        }).catch((error) => {

            flash(error.message)

        }).then(()=>{

          this.$emit('loading',false)

        })

      },

      btnShowTask(task){

        this.taskForEdit.task = task;
        this.taskForEdit.editing = false;
        this.taskForEdit.owner = this.users[task.user_id-1].name;

      },

      btnEditTask(task, index){

        this.usedTask = task;
        this.taskForEdit.task = task;
        this.taskForEdit.index = index;
        this.taskForEdit.editing = true;
      },

      btnDeleteTask(task) {


          this.$emit('loading', true)
          this.taskBeingDeleted = task.id
          axios.delete('/api/v1/tasks/' + task.id).then(() => {
            this.tasks.splice(this.tasks.indexOf(task), 1)
          }).catch((error) => {
            flash(error.message)
          }).then(() => {
            this.$emit('loading', false)
          })
        },

    },

    mounted() {

      this.loading = true;

      axios.get(API_URL_USERS).then((response) =>  {
        this.users = response.data;

      }).catch((error) => {
        flash(error.message)
      }).then(()=>{

        this.loading = false
        console.log(this.users);

      })

      this.loading = true;

      axios.get(API_URL).then((response) =>  {
        this.tasks = response.data;

      }).catch((error) => {
        flash(error.message)
      }).then(()=>{

        this.loading = false
      })




    }
  }
</script>
