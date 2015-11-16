class ReactionsController < ApplicationController
  before_action :set_reaction, only: :destroy

  def index
    # 액션 갯수 구하기
    action_count = {}
    actions = UserAction.where(tracking_id: params[:tracking_id])
    # 너무 적으면 생성하지 않음
    if actions.size < 20
      render json: { reaction: 'none' }
      return
    end
    for action in actions
      if action_count.has_key? action.name
        action_count[action.name] += 1
      else
        action_count[action.name] = 1
      end
    end
    # 갯수로 sort
    action_count = action_count.sort_by { |action, count| count }
    # 가장 빈도수 높은 액션
    action_name = action_count.reverse[0][0]
    # 리액션 가중치 두고 구하기
    reactions = Score.where(action_name: action_name)
    total_score = 0
    reactions.each do |r| total_score += r.score end
    value = rand(total_score * 2)
    score = 0
    for reaction in reactions
      score += reaction.score
      if value < score
        render json: { reaction: reaction.reaction_id }
        return
      end
    end
    render json: { reaction: 'none' }
  end

  def create
    @reaction = Reaction.new(reaction_params)
    if @reaction.save
      render json: { result: 'success' }
    else
      render json: @reaction.errors, status: :unprocessable_entity
    end
  end

  def destroy
    @reaction.destroy
    render json: { result: 'success' }
  end

  def recommand
    AnalysisJob.perform_later
    render json: { result: 'success' }
  end

  private
    def set_reaction
      @reaction = Reaction.find(params[:id])
    end

    def reaction_params
      params.require(:reaction).permit(:description)
    end
end
