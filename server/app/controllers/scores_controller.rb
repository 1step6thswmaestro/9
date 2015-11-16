class ScoresController < ApplicationController
  before_action :set_score, only: :create

  def create
    if @score
      score = @score[:score] > 1 ? @score[:score] : 1
      score += score_params[:successful] ? 1 : -1
      if @score.update(score: score)
        render json: { result: 'success' }
      else
        render json: @score.errors
      end
    else
      @score = Score.new({
        :action_name => score_params[:action_name],
        :reaction_id => score_params[:reaction_id],
        :score => 1})
      if @score.save
        render json: { result: 'success' }
      else
        render json: @score.errors
      end
    end
  end

  private
    def set_score
      @score = Score.where(
        :action_name => score_params[:action_name],
        :reaction_id => score_params[:reaction_id])[0]
    end

    def score_params
      params.require(:score).permit(:action_name, :reaction_id, :successful)
    end
end
