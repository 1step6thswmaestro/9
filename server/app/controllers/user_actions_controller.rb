class UserActionsController < ApplicationController
  def create
    @user_action = UserAction.new(user_action_params)

    if @user_action.save
      render json: { result: 'success', notice: 'User action was successfully created.' }
    else
      render json: @user_action.errors, status: :unprocessable_entity
    end
  end

  private
    def user_action_params
      params.require(:user_action).permit(:tracking_id, :name, :value)
    end
end
