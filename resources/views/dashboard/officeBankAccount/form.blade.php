form action="{{ route('email-template.store') }}" method="POST">
                            @csrf


                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="hf-email">Template Name</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="name" type="text" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="hf-password">Template Description</label>
                                <div class="col-md-9">
                                    <textarea class="form-control" id="desc" name="desc" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="hf-password">Template Short Code</label>
                                <div class="col-md-9">
                                    <input type="text" name="code" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="hf-password">Email Subject</label>
                                <div class="col-md-9">
                                    <input type="text" name="subject" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="hf-password"></label>
                                <div class="col-md-3 col-form-label">
                                    <strong>Email To</strong>
                                    @foreach($roles as $index => $role)

                                    <div class="form-check checkbox">
                                        <input class="form-check-input" name="emailTo[]" type="checkbox" value="{{$role->id }}">
                                        <label class="form-check-label" for="check1">{{$role->name }}</label>
                                    </div>
                                    @endforeach
                                </div>

                                <!-- <label class="col-md-3 col-form-label" for="hf-password">Email Subject</label> -->
                                <div class="col-md-3 col-form-label">
                                    <strong>Email From</strong>


                                    @foreach($roles as $index => $role)

                                    <div class="form-check checkbox">
                                        <input class="form-check-input" name="emailFrom[]" type="checkbox" value="{{$role->id }}">
                                        <label class="form-check-label" for="check1">{{$role->name }}</label>
                                    </div>
                                    @endforeach
                                </div>

                                <!-- <label class="col-md-3 col-form-label" for="hf-password">Email Subject</label> -->
                                <div class="col-md-3 col-form-label">
                                    <strong>Email CC</strong>
                                    @foreach($roles as $index => $role)

                                    <div class="form-check checkbox">
                                        <input class="form-check-input" name="emailCC[]" type="checkbox" value="{{$role->id }}">
                                        <label class="form-check-label" for="check1">{{$role->name }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>


                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="hf-password">Status</label>
                                <div class="col-md-9"><select class="form-control" id="status" name="status">
                                        <option value="0">Please select</option>
                                        <option value="1">Active</option>
                                        <option value="2">Draft</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="hf-password">Content</label>
                                <div class="col-md-9">
                                    <textarea class="form-control" id="summary-ckeditor" name="summary-ckeditor"></textarea>
                                </div>
                            </div>

                            <button class="btn btn-primary float-right" type="submit">Save</button>
                            <a class="btn btn-primary" href="{{ route('email-template.index') }}">Return</a>
                        </form>